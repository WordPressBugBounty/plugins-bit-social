<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Requests\ScheduleStoreRequest;
use BitApps\Social\HTTP\Services\Schedule\ScheduleInfo;
use BitApps\Social\Model\Schedule;
use BitApps\Social\Providers\ScheduleActionHook;
use BitApps\Social\Utils\Common;
use BitApps\Social\Utils\Creator;
use BitApps\Social\Utils\ScheduleSummary;
use BitApps\Social\Utils\SmartTag;
use BitApps\SocialPro\Config as ProConfig;
use DateTime;

class ScheduleController
{
    use Common;

    public function index($page, $limit)
    {
        $schedules = Schedule::where('schedule_type', Schedule::scheduleType['SCHEDULE_SHARE'])
            ->select(['id', 'name', 'config', 'started_at', 'next_published_at', 'status', 'created_at'])
            ->desc()
            ->paginate($page, $limit);

        foreach ($schedules['data'] as $schedule) {
            Creator::attachToSchedule($schedule);
            ScheduleSummary::attachToSchedule($schedule);

            $actionHook = Config::VAR_PREFIX . $schedule->id . '_cron_exec';
            $hookArgument['schedule_id'] = $schedule->id;
            $nextPostTimeStamp = wp_next_scheduled($actionHook, [$hookArgument]);

            if ($nextPostTimeStamp) {
                $actionHook = Config::VAR_PREFIX . $schedule->id . '_cron_exec';
                $hookArgument['schedule_id'] = $schedule->id;

                $nextPostTimeStamp = wp_next_scheduled($actionHook, [$hookArgument]);
                $exactDateTime = get_date_from_gmt(gmdate('Y-m-d H:i:s', $nextPostTimeStamp), 'Y-m-d H:i:s');
                $nextPostTimeStamp = strtotime($exactDateTime);

                $currentTimestamp = current_time('timestamp');

                $humanReadableTime = human_time_diff($currentTimestamp, $nextPostTimeStamp);

                $schedule->human_readable_next_publish = 'in ' . $humanReadableTime;
                $schedule->next_published_at = $exactDateTime;
            }
        }

        return Response::success($schedules);
    }

    public function search(Request $request, $search, $page, $limit)
    {
        $validatedData = (object) $request->validate([
            'search' => ['required', 'string'],
            'page'   => ['required', 'integer'],
            'limit'  => ['required', 'integer'],
        ]);

        $schedules = Schedule::where('name', 'like', '%' . $validatedData->search . '%')->where('schedule_type', Schedule::scheduleType['SCHEDULE_SHARE'])
            ->select(['id', 'name', 'config', 'started_at', 'next_published_at', 'status', 'created_at'])
            ->desc()
            ->paginate($validatedData->page, $validatedData->limit);

        foreach ($schedules['data'] as $schedule) {
            Creator::attachToSchedule($schedule);
            ScheduleSummary::attachToSchedule($schedule);
        }

        return Response::success($schedules);
    }

    public function show(Schedule $schedule)
    {
        if ($schedule->exists()) {
            return $schedule;
        }

        return Response::error($schedule);
    }

    public function store(ScheduleStoreRequest $request)
    {
        if (!$this->canUseRequestedAccounts($request->accounts)) {
            return $this->accountsNotPermittedResponse();
        }

        $scheduleData = [
            'name'   => $request->settings['name'],
            'config' => [
                'settings'     => $request->settings,
                'post_filters' => $request->post_filters,
                'accounts'     => $request->accounts,
                'templates'    => $request->templates,
                // Records who created this schedule so the Logs page can show
                // the responsible user/role. Preserved on update() below.
                'created_by' => get_current_user_id(),
            ],
            'schedule_type' => Schedule::scheduleType['SCHEDULE_SHARE'],
            'status'        => Schedule::status['ACTIVE'],
            'started_at'    => $request->settings['started_at'],
        ];

        $startedAtTimestamp = !empty($scheduleData['started_at']) ? strtotime($scheduleData['started_at']) : false;
        $currentTimestamp = current_time('timestamp');

        if ($startedAtTimestamp && $currentTimestamp < $startedAtTimestamp) {
            $scheduleData['next_published_at'] = $scheduleData['started_at'];
        } else {
            $scheduleData['next_published_at'] = null;
        }

        Schedule::insert($scheduleData);

        return Response::success('Schedule created successfully');
    }

    public function update(ScheduleStoreRequest $request, Schedule $schedule)
    {
        if (!$this->canUseRequestedAccounts($request->accounts)) {
            return $this->accountsNotPermittedResponse();
        }

        $existingConfig = \is_string($schedule->config) ? json_decode($schedule->config, true) : (array) $schedule->config;

        $scheduleData = [
            'name'   => $request->settings['name'],
            'config' => [
                'settings'     => $request->settings,
                'post_filters' => $request->post_filters,
                'accounts'     => $request->accounts,
                'templates'    => $request->templates,
                // Keep the original creator; only fall back to the current user
                // for schedules created before attribution existed.
                'created_by' => $existingConfig['created_by'] ?? get_current_user_id(),
            ],
            'started_at' => $request->settings['started_at'],
        ];

        $scheduleData['last_published_at'] = $schedule->last_published_at;

        $startedAtTimestamp = !empty($scheduleData['started_at']) ? strtotime($scheduleData['started_at']) : false;
        $currentTimestamp = current_time('timestamp');

        if ($startedAtTimestamp && $currentTimestamp < $startedAtTimestamp) {
            $scheduleData['next_published_at'] = $scheduleData['started_at'];
        } else {
            $scheduleData['next_published_at'] = null;
        }

        unset($scheduleData['last_published_at']);

        $copyNewScheduleData = $scheduleData['config']['settings'];
        $copyOldScheduleData = $schedule['config']['settings'];

        unset($copyOldScheduleData['name'], $copyNewScheduleData['name']);

        $areEqual = (serialize($copyOldScheduleData) === serialize($copyNewScheduleData));

        $currentSchedule = \is_string($schedule->config) ? json_decode($schedule->config, true) : $schedule->config;

        if ($currentSchedule['post_filters'] !== $request->post_filters) {
            $scheduleData['published_post_ids'] = null;
            $scheduleData['cron_status'] = Schedule::cronStatus['ACTIVE'];
        }

        $schedule->update($scheduleData);

        if ($schedule->save()) {
            if ((int) $schedule['status'] === Schedule::status['ACTIVE'] && !$areEqual) {
                $this->removeScheduleHook($schedule['id']);
            }

            return Response::success('Schedule updated');
        }

        return Response::error('Schedule update failed');
    }

    public function updateStatus(Request $request, Schedule $schedule)
    {
        // Only manage a schedule whose accounts the current user may use —
        // same per-account gate as creating it (admins/free pass).
        if (!$this->canManageSchedules([$schedule->id])) {
            return $this->accountsNotPermittedResponse();
        }

        $validatedData = (object) $request->validate([
            'status' => ['required', 'integer'],
        ]);

        $requestedStatus = $validatedData->status;

        $message = $requestedStatus === 1 ? 'active' : 'paused';

        if ($requestedStatus === $schedule->status) {
            return Response::success('Schedule already ' . $message . '!');
        }

        if ($schedule->status === Schedule::status['COMPLETED']) {
            return Response::error('Schedule already completed');
        }

        $settings = $schedule->config['settings'];
        $repeat = $settings['repeat'] ?? false;

        if ((int) $schedule->schedule_type === Schedule::scheduleType['DIRECT_SHARE'] && !$repeat && strtotime($schedule->started_at) < current_time('timestamp')) {
            $savedSchedule = $schedule->update(['status' => Schedule::status['MISSED']]);

            if ($savedSchedule->save()) {
                $this->removeScheduleHook($schedule['id']);

                return Response::error('Oops! Missed the start post time! Please, update start time or create new one!');
            }
        } else {
            $savedSchedule = $schedule->update(['status' => $requestedStatus]);
        }

        if ($savedSchedule->save()) {
            if ($requestedStatus === Schedule::status['INACTIVE']) {
                $this->removeScheduleHook($schedule['id']);
            } elseif (Schedule::scheduleType['DIRECT_SHARE'] === (int) $schedule->schedule_type && $requestedStatus === Schedule::status['ACTIVE'] && !$repeat) {
                $this->createSingleEventScheduleIsNotRepeat($schedule);
            } elseif ($requestedStatus === Schedule::status['ACTIVE'] && $repeat) {
                $this->removeScheduleHook($schedule->id);
            }

            return Response::success('Schedule status updated');
        }

        return Response::error('Schedule status update failed');
    }

    /**
     * Publish a MISSED schedule now.
     *
     * A schedule only reaches MISSED because WP-Cron did not run in time, so the
     * retry publishes inline instead of queueing another cron event — handing it
     * back to the scheduler would strand it exactly the same way. Works for both
     * Share Now rows and auto-post/scheduled-share rows.
     */
    public function retry(Schedule $schedule)
    {
        if ((int) $schedule->status !== Schedule::status['MISSED']) {
            return Response::error('Only a missed post can be published again.');
        }

        // The free plugin refuses to publish a schedule created on an earlier
        // day, so say that plainly here — otherwise publishPost() bails on it
        // silently and the generic "nothing was published" message below would
        // blame the post or the accounts for a licensing limit.
        $info = new ScheduleInfo(['schedule_id' => $schedule->id]);

        if ($info->config() && !$this->isProActive() && !$info->isCreatedToday()) {
            return Response::error('The free plugin can only publish schedules created today. Upgrade to Pro to republish an older one.');
        }

        $wpNow = gmdate('Y-m-d H:i:s', current_time('timestamp'));

        $revived = $schedule->update([
            'status'            => Schedule::status['ACTIVE'],
            'cron_status'       => Schedule::cronStatus['ACTIVE'],
            'started_at'        => $wpNow,
            'next_published_at' => $wpNow,
            'ended_at'          => null,
        ]);

        if (!$revived->save()) {
            return Response::error('Could not reopen the missed post.');
        }

        // publishPost() has several silent bail-outs (source post gone, accounts
        // removed, sleep window). Watching the publish action is the only
        // reliable signal that something actually went out — the row's end
        // status alone does not distinguish "published" from "gave up".
        $publishResults = null;
        $publishHook = Config::withPrefix('all_platforms_post_publish');
        $capture = static function ($results) use (&$publishResults) {
            $publishResults = $results;
        };

        Hooks::addAction($publishHook, $capture);
        ScheduleActionHook::execute(['schedule_id' => $schedule->id]);
        Hooks::removeAction($publishHook, $capture);

        if (empty($publishResults)) {
            // Put it back to MISSED so it stays visible and retryable rather
            // than hanging in ACTIVE ("In Progress") or looking completed.
            $schedule->update([
                'status'            => Schedule::status['MISSED'],
                'cron_status'       => Schedule::cronStatus['INACTIVE'],
                'ended_at'          => $wpNow,
                'next_published_at' => null,
            ])->save();

            $this->removeScheduleHook($schedule->id);

            return Response::error('Nothing was published — the post or its accounts may no longer exist. Check the logs.');
        }

        return Response::success('Post published. Check the logs for per-platform results.');
    }

    public function destroy(Request $request)
    {
        // Block deleting schedules that post through accounts the current user
        // isn't permitted on (per-account gate; admins/free pass).
        if (!$this->canManageSchedules($request->scheduleIds)) {
            return $this->accountsNotPermittedResponse();
        }

        Schedule::whereIn('id', $request->scheduleIds)->delete();

        $this->removeScheduleHook($request->scheduleIds);

        return Response::success('Selected schedules deleted');
    }

    public function getScheduleNextPostTime($schedule)
    {
        $schedule = json_decode(json_encode($schedule));
        $config = $schedule->config;

        if (isset($config->post_filters)) {
            $postsFilterDays = $config->post_filters->filter_by_days; // 0 - all times
            $postsOrderType = $config->settings->post_publish_order; // 2 - randomly

            $postsCount = -1;

            if ($postsFilterDays !== '0' && $postsOrderType !== '2') {
                $postsController = new WpPostController();
                $filteredPosts = $postsController->filterPosts($config->post_filters);
                $postsCount = \count($filteredPosts);
            }

            $hasPostsCountLimit = $postsCount !== -1;

            if ($hasPostsCountLimit) {
                if ($postsCount === 0) {
                    return;
                }

                if (isset($schedule->published_post_ids) && \is_array($schedule->published_post_ids) && $postsCount <= \count($schedule->published_post_ids)) {
                    return;
                }
            }
        }

        if (isset($schedule->last_published_at) && !\is_null($schedule->last_published_at)) {
            $nextPostTime = $schedule->last_published_at;
        } else {
            $nextPostTime = $schedule->started_at;
        }

        $interval = [
            'every' => (int) $config->settings->post_interval_value,
            'unit'  => $config->settings->post_interval_type
        ];

        $sleepTime = [];
        $sleepDays = [];

        if (isset($config->settings->sleep_time) && !empty($config->settings->sleep_time)) {
            $start = $config->settings->sleep_time[0];
            $end = $config->settings->sleep_time[1];

            $sleepTime = [
                'start' => $start,
                'end'   => $end
            ];
        }
        if (isset($config->settings->sleep_days) && !empty($config->settings->sleep_days)) {
            $sleepDays = $config->settings->sleep_days;
        }

        return $this->getNextInterval($nextPostTime, $interval, $sleepTime, $sleepDays);
    }

    /**
     * Every schedule, with its next firing time where it still has one.
     *
     * A pending cron event used to be the price of admission — a schedule was
     * only returned if wp_next_scheduled() knew about it — so a finished, paused
     * or completed schedule vanished from this list entirely. That is most of
     * them: it left the Logs screen's schedule filter offering one option out of
     * nineteen, and the Calendar logging "schedule details not found" for any
     * row it could not look up. Whether a schedule will fire again is a property
     * to report, not a reason to omit it.
     */
    public function allSchedule()
    {
        $schedules = Schedule::get(['id', 'name', 'config', 'schedule_type', 'started_at', 'next_published_at', 'status', 'created_at']);

        $data = [];

        foreach ($schedules as $schedule) {
            $actionHook = Config::VAR_PREFIX . $schedule->id . '_cron_exec';
            $hookArgument = ['schedule_id' => $schedule->id];
            $nextPostTimeStamp = wp_next_scheduled($actionHook, [$hookArgument]);

            if ($nextPostTimeStamp) {
                $exactDateTime = get_date_from_gmt(gmdate('Y-m-d H:i:s', $nextPostTimeStamp), 'Y-m-d H:i:s');

                $schedule->human_readable_next_publish = 'in ' . human_time_diff(current_time('timestamp'), strtotime($exactDateTime));
                $schedule->next_published_at = $exactDateTime;
            }

            $data[] = $schedule;
        }

        return Response::success($data);
    }

    public function getSmartTags()
    {
        if (SmartTag::$smartTagNameList) {
            return Response::success(SmartTag::$smartTagNameList);
        }

        return Response::error('Smart tag error');
    }

    /**
     * Whether the Pro add-on is active and licensed. Mirrors the gate
     * SocialExecution applies before it enforces the free day limit.
     *
     * @return bool
     */
    private function isProActive()
    {
        return class_exists('BitApps\SocialPro\Config') && ProConfig::isPro();
    }

    private function getNextInterval($startDate, $interval, $sleepTime, $sleepDays)
    {
        $currentDate = new DateTime($startDate);

        switch ($interval['unit']) {
            case 'second':
                $currentDate->modify("+{$interval['every']} seconds");

                break;
            case 'minute':
                $currentDate->modify("+{$interval['every']} minutes");

                break;
            case 'hour':
                $currentDate->modify("+{$interval['every']} hours");

                break;
            case 'day':
                $currentDate->modify("+{$interval['every']} days");

                break;
            case 'week':
                $currentDate->modify("+{$interval['every']} weeks");

                break;
            case 'month':
                $currentDate->modify("+{$interval['every']} months");

                break;
            case 'year':
                $currentDate->modify("+{$interval['every']} years");

                break;
            default:
                break;
        }

        $weekDay = $currentDate->format('D');
        $isSleepDay = $sleepDays && \in_array($weekDay, $sleepDays);
        $isSleepTime = false;

        if ($sleepTime && $sleepTime['start'] && $sleepTime['end']) {
            $sleepStartTime = new DateTime($currentDate->format('Y-m-d'));
            $sleepEndTime = new DateTime($currentDate->format('Y-m-d'));

            list($startHour, $startMinute, $startSecond) = explode(':', $sleepTime['start']);
            list($endHour, $endMinute, $endSecond) = explode(':', $sleepTime['end']);

            $sleepStartTime->setTime($startHour, $startMinute, $startSecond);
            $sleepEndTime->setTime($endHour, $endMinute, $endSecond);

            if ($currentDate >= $sleepStartTime && $currentDate <= $sleepEndTime) {
                $isSleepTime = true;
            }
        }
        if (!$isSleepDay && !$isSleepTime) {
            return $currentDate->format('Y-m-d H:i:s');
        }

        if (!$interval['every']) {
            return $currentDate->format('Y-m-d H:i:s');
        }

        return $this->getNextInterval($currentDate->format('Y-m-d H:i:s'), $interval, $sleepTime, $sleepDays);
    }

    private function createSingleEventScheduleIsNotRepeat($schedule)
    {
        if (isset($schedule['config']['settings']['repeat']) && $schedule['config']['settings']['repeat'] === true) {
            return;
        }

        $scheduleRunTime = time();
        $wpTimeStamp = current_time('timestamp');
        $actionHook = Config::VAR_PREFIX . $schedule['id'] . '_cron_exec';
        $hookArgument['schedule_id'] = $schedule['id'];

        $wpNextSchedule = wp_next_scheduled($actionHook, [$hookArgument]); // it returns timestamp value or false

        if (!empty($schedule['started_at']) && strtotime($schedule['started_at']) > $wpTimeStamp) {
            $delay = strtotime($schedule['started_at']) - $wpTimeStamp;
            $scheduleRunTime = $scheduleRunTime + $delay;
        } elseif ($wpNextSchedule && !empty($schedule['started_at']) && strtotime($schedule['started_at']) < $wpTimeStamp) {
            $scheduleRunTime = $wpNextSchedule;
        }

        return wp_schedule_single_event($scheduleRunTime, $actionHook, [$hookArgument]);
    }
}
