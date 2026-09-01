<?php

namespace BitApps\Social\HTTP\Services\Schedule;

use BitApps\Social\HTTP\Services\Traits\LoggerTrait;
use BitApps\Social\Model\Log;
use BitApps\Social\Utils\Common;
use BitApps\Social\Utils\LogRun;

/**
 * Writes the log rows for a slot that passed without a publish attempt.
 *
 * There are two ways that happens and both have to leave the same trail, or the
 * Logs screen would show one kind of miss and silently drop the other:
 *
 * - a recurring schedule whose cron event fired far too late
 *   (SocialExecution::logMissedRun, via ScheduleInfo::isExecutionStale)
 * - a one-shot — a delayed auto-post or a Share Now — written off before its
 *   event ever ran (CustomSchedule::reapMissedSingleEvents)
 *
 * The second never reaches SocialExecution at all, so the rows are built here
 * rather than there.
 */
final class MissedRunLogger
{
    use Common, LoggerTrait;

    /**
     * One row per account, matching the shape a real publish writes, because
     * that is what makes the row retryable: RetryController reads account_id and
     * post_id straight off it and republishes to that one account. A single
     * per-schedule row would look tidier and be useless.
     *
     * status is FAIL rather than a status of its own — the column is a boolean.
     * details.reason is what tells a missed slot apart from a platform refusing
     * the post, and it is what the Logs filter matches on.
     *
     * @param int         $scheduleId
     * @param array       $accountIds accounts table row ids
     * @param null|int    $postId     the post this slot would have published
     * @param null|string $missedSlot the slot itself, WP-local wall-clock
     *
     * @return int how many rows were written
     */
    public function log($scheduleId, $accountIds, $postId, $missedSlot)
    {
        if (empty($accountIds)) {
            return 0;
        }

        // Opens a run so every account of one miss shares a run id and the Logs
        // screen shows it as a single line. When called from inside an open run
        // (SocialExecution's) the outer id is kept and left for its owner to
        // close — ending it here would split whatever it logs next.
        $ownsRun = !LogRun::isOpen();
        LogRun::start();

        try {
            foreach ($accountIds as $account) {
                $isPlatFormExists = $this->isPlatFormExists($account);
                $accountDetails = $isPlatFormExists ? $isPlatFormExists['details'] : null;

                $this->logCreate([
                    'schedule_id' => $scheduleId,
                    'platform'    => $isPlatFormExists ? $isPlatFormExists['platform'] : 'unknown',
                    'status'      => Log::status['FAIL'],
                    'details'     => [
                        // The platform's own id, which is what a published row
                        // records and what RetryController looks the account up
                        // by. $account is the accounts table's row id: logging
                        // that instead left every missed row un-retryable,
                        // because the retry could never find the account it named.
                        'account_id'   => isset($accountDetails->account_id) ? $accountDetails->account_id : $account,
                        'account_name' => isset($accountDetails->account_name) ? $accountDetails->account_name : 'unknown',
                        'post_id'      => $postId,
                        'reason'       => Log::reason['MISSED'],
                        'scheduled_at' => $missedSlot,
                        'response'     => 'Scheduled time passed without publishing — WP-Cron ran too late. Retry to publish it now.',
                    ],
                ]);
            }
        } finally {
            if ($ownsRun) {
                LogRun::end();
            }
        }

        return \count($accountIds);
    }
}
