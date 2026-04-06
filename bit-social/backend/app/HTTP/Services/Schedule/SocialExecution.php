<?php

namespace BitApps\Social\HTTP\Services\Schedule;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\HTTP\Services\Social\Social;
use BitApps\Social\HTTP\Services\Traits\LoggerTrait;
use BitApps\Social\Model\Schedule;
use BitApps\Social\Utils\Common;
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
        $currentDay = gmdate('D');
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

    public function publishPost()
    {
        if (!$this->schedule->config()) {
            return false;
        }

        if (!$this->isSleep($this->schedule->settings())) {
            return false;
        }

        if (empty($this->schedule->accounts())) {
            return false;
        }
        if (empty($this->schedule->scheduleType())) {
            return false;
        }

        if ((!class_exists('BitApps\SocialPro\Config') || !ProConfig::isPro()) && !$this->schedule->isScheduleCreatedToday()) {
            return false;
        }

        if ($this->schedule->scheduleType === Schedule::scheduleType['DIRECT_SHARE']) {
            $config = $this->schedule->config();
            $repeat = $config['settings']['repeat'];

            if ($repeat) {
                $this->schedule->nextPostUpdate();
            } else {
                $this->schedule->scheduleComplete();
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
                $status = $this->schedule->scheduleComplete();

                if ($status) {
                    $this->removeScheduleHook($this->schedule->scheduleId);
                }
            }

            if ($postPublishOrders) {
                $this->schedule->nextPostUpdate();
            }

            if (!$post) {
                return;
            }
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

            if (isset($templates[$platformName])) {
                $template = $templates[$platformName];

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
}
