<?php

namespace BitApps\Social\HTTP\Services\Schedule;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\HTTP\Services\Social\Social;
use BitApps\Social\HTTP\Services\Traits\LoggerTrait;
use BitApps\Social\Model\Schedule;
use BitApps\Social\Utils\Common;
use BitApps\Social\Utils\LogRun;
use BitApps\Social\Utils\PublishLock;
use BitApps\Social\Utils\WpPost;
use BitApps\SocialPro\Config as ProConfig;

final class SocialExecution
{
    use WpPost, Common, LoggerTrait;

    protected $schedule;

    public function __construct(ScheduleInfo $schedule)
    {
        $this->schedule = $schedule;
    }

    public function isSleep($settings)
    {
        // Site time, like the sleep time below and the next run time in the UI.
        $currentDay = current_time('D');
        $sleepTime = isset($settings['sleep_days']) ? $settings['sleep_days'] : [];

        if (\in_array($currentDay, $sleepTime)) {
            return false;
        }

        $sleepTime = isset($settings['sleep_time']) ? $settings['sleep_time'] : [];

        if (!empty($sleepTime[0]) && !empty($sleepTime[1])) {
            $currentTime = current_time('H:i:s');
            $startTime = $sleepTime[0];
            $endTime = $sleepTime[1];

            if ($startTime <= $endTime) {
                // Standard range (e.g., 20:00 - 23:00)
                if ($currentTime >= $startTime && $currentTime <= $endTime) {
                    return false;
                }
            } else {
                // Overnight range (e.g., 22:00 - 03:00)
                if ($currentTime >= $startTime || $currentTime <= $endTime) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * One run of a schedule: pick the post, send it to every account, log what
     * happened.
     *
     * Wrapped in a log run so all the rows it writes — one per account, plus any
     * missed or account-not-found entries — carry the same id and the Logs
     * screen shows them as a single line rather than one line per account.
     *
     * @return null|bool
     */
    public function publishPost()
    {
        // Tells the missed-post reaper that this schedule is busy, not stuck:
        // the event is already consumed and the row still says ACTIVE, so from
        // the outside a run in progress and a run that never happened look the
        // same. Released in the same breath as the log run.
        //
        // It also stops a second run. Overlapping cron requests (an external
        // cron and a spawned WP-Cron) can fire the same event at once, and each
        // run would publish a different post.
        if (!PublishLock::acquire($this->schedule->scheduleId)) {
            return false;
        }

        LogRun::start();

        try {
            return $this->runPublish();
        } finally {
            PublishLock::release($this->schedule->scheduleId);
            LogRun::end();
        }
    }

    private function runPublish()
    {
        if (!$this->schedule->config()) {
            return false;
        }

        if (!$this->isSleep($this->schedule->settings())) {
            return $this->haltWithoutPublishing();
        }

        if (empty($this->schedule->accounts())) {
            return $this->haltWithoutPublishing();
        }
        if (empty($this->schedule->scheduleType())) {
            return $this->haltWithoutPublishing();
        }

        if ((!class_exists('BitApps\SocialPro\Config') || !ProConfig::isPro()) && !$this->schedule->isScheduleCreatedToday()) {
            return false;
        }

        // A run WP-Cron fired long after its slot (catching up after downtime)
        // must not publish — otherwise every overdue schedule fires at once and
        // dumps a burst on the platform. It is decided here but acted on at the
        // end: the run still does all its bookkeeping, so the slot is consumed
        // and the schedule keeps the timetable it was given. Only the publish
        // itself is replaced, by a missed entry in the log.
        $isStale = $this->schedule->isExecutionStale();
        $missedSlot = $isStale ? $this->schedule->intendedSlot() : null;
        $post = null;

        if ($this->schedule->scheduleType === Schedule::scheduleType['DIRECT_SHARE']) {
            $config = $this->schedule->config();
            $repeat = $config['settings']['repeat'];

            if ($repeat) {
                $this->schedule->nextPostUpdate($missedSlot);
            } else {
                $this->schedule->scheduleComplete($isStale);
            }
        }

        if ($this->schedule->scheduleType === Schedule::scheduleType['SCHEDULE_SHARE']) {
            $filterArgument = $this->schedule->postFilterArgument();
            $postPublishOrders = (int) $this->schedule->settings()['post_publish_order'] ?? null;
            $post = $this->getPost($filterArgument);
            $orderTypeRandomly = 2;
            $orderTypeLatest = 5;

            if (!empty($post['id'])) {
                $this->schedule->postIdUpdate($post);
            }
            $filterArgument = $this->schedule->postFilterArgument();
            $nextPost = $this->getPost($filterArgument);
            $isNextPost = !empty($nextPost['id']);

            $specificPostIds = $this->schedule->postFilters()['specific_postIds'] ?? [];
            $postPublishIds = $this->schedule->publishedPostIds();

            $isOrderTypeRandomly = $postPublishOrders === $orderTypeRandomly;
            $isOrderTypeLatest = $postPublishOrders === $orderTypeLatest;

            $isSpecificPostIdsAllPosted = !empty($specificPostIds) && empty(array_diff($specificPostIds, $postPublishIds));

            $shouldCompleteSchedule = !$isNextPost && !$isOrderTypeRandomly && !$isOrderTypeLatest || $isSpecificPostIdsAllPosted && !$isOrderTypeRandomly;

            if ($shouldCompleteSchedule) {
                // $isStale: this run is retiring the schedule without publishing,
                // and its missed rows are not written until below — so the
                // terminal state has to be told about it rather than count it.
                $status = $this->schedule->scheduleComplete($isStale);

                if ($status) {
                    $this->removeScheduleHook($this->schedule->scheduleId);
                }
            }

            if ($postPublishOrders) {
                $this->schedule->nextPostUpdate($missedSlot);
            }

            if (!$post) {
                return;
            }
        }

        // Everything above has already run: the post was taken out of the queue
        // and the schedule moved on. All that is left is to record why it never
        // reached a platform, so the Logs screen can show and retry it.
        if ($isStale) {
            return $this->logMissedRun($post, $missedSlot);
        }

        $templates = $this->schedule->templates();
        $publishPostData = [];

        foreach ($this->schedule->accounts as $account) {
            $isPlatFormExists = $this->isPlatFormExists($account);

            if (!$isPlatFormExists) {
                $error_data = [
                    'schedule_id' => $this->schedule->scheduleId,
                    'details'     => [
                        'account_id'   => $account,
                        'account_name' => 'unknown',
                        'post_id'      => $post['id'],
                        'response'     => 'Account not found or disable',
                    ],
                    'platform' => 'unknown',
                    'status'   => 0,
                ];

                if ($this->schedule->scheduleType === Schedule::scheduleType['SCHEDULE_SHARE']) {
                    $error_data['details']['post_id'] = $post['id'];
                }
                $this->logCreate($error_data);

                continue;
            }

            $platformName = $isPlatFormExists['platform'];

            $platform = new Social(new $isPlatFormExists['class']());

            if (!$isPlatFormExists) {
                continue;
            }

            $template = isset($templates[$platformName]) ? $templates[$platformName] : null;

            if (!empty($template)) {
                $data = [
                    'template'        => $template,
                    'account_details' => $isPlatFormExists['details'],
                    'schedule_id'     => $this->schedule->scheduleId,
                    'schedule_type'   => $this->schedule->scheduleType,
                ];

                if ($this->schedule->scheduleType === Schedule::scheduleType['SCHEDULE_SHARE']) {
                    $data['post'] = (array) get_post($post['id']);
                    $data['post']['ID'] = $post['id'];
                }

                $publishPostData[] = $platform->publishPost($data);
            }
        }

        Hooks::doAction(Config::withPrefix('all_platforms_post_publish'), $publishPostData);
    }

    /**
     * Record a slot that passed without a publish attempt.
     *
     * The rows themselves are MissedRunLogger's job — a one-shot written off by
     * the reaper never reaches this class and has to leave the same trail.
     *
     * @param null|array  $post       the post this slot would have published
     * @param null|string $missedSlot the slot itself, WP-local wall-clock
     *
     * @return false
     */
    private function logMissedRun($post, $missedSlot)
    {
        (new MissedRunLogger())->log(
            $this->schedule->scheduleId,
            $this->schedule->accounts,
            !empty($post['id']) ? $post['id'] : null,
            $missedSlot
        );

        // Same action the one-shot reaper fires, so the Pro alert reports a
        // missed slot the same way it reports a written-off post.
        Hooks::doAction(Config::withPrefix('schedules_missed'), [$this->schedule->scheduleId]);

        return false;
    }

    /**
     * Bail out of a run without publishing. A single-event (non-recurring)
     * schedule has no future cron event, so bailing would leave it stuck ACTIVE
     * ("In Progress") forever — mark it MISSED instead so it reaches a terminal,
     * visible state. Recurring schedules simply skip this tick and fire again.
     *
     * @return false
     */
    private function haltWithoutPublishing()
    {
        if (!$this->schedule->isRecurring()) {
            $this->schedule->scheduleMissed();
        }

        return false;
    }
}
