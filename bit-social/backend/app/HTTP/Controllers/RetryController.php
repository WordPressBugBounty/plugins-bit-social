<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\Model\Account;
use BitApps\Social\Model\Schedule;
use BitApps\Social\Utils\Common;
use BitApps\Social\Utils\WpPost;

class RetryController
{
    use Common, WpPost;

    /**
     * Why the last publishFromLog() call gave up.
     *
     * The per-row retry reports it to the user, who can then act on it —
     * "Account not found" is a different problem from "Template not found".
     * Retrying a whole run ignores it: one bad account among ten should not
     * decide the message for all of them.
     *
     * @var null|string
     */
    private $lastError;

    public function retry(Request $request)
    {
        $validated = $request->validate([
            'key'        => ['required'],
            'scheduleId' => ['required'],
            'details'    => ['required'],
        ]);

        $result = $this->publishFromLog(
            $validated['scheduleId'],
            (array) $validated['details'],
            $validated['key']
        );

        if (empty($result)) {
            return Response::error($this->lastError ?: 'Retry failed');
        }

        return $result;
    }

    /**
     * Republish one account's share from what its log row recorded.
     *
     * Shared by the per-row retry button and by retrying a whole run
     * (LogController::retryRun), so a run of ten accounts is one call per
     * account through the same path rather than a second implementation.
     *
     * The platform services update the row identified by $logId instead of
     * writing a new one, so a successful retry turns the failure green rather
     * than leaving a failed row and a successful row side by side.
     *
     * @param int   $scheduleId
     * @param array $details    the log row's details
     * @param int   $logId
     *
     * @return mixed false when the retry could not even be attempted
     */
    public function publishFromLog($scheduleId, $details, $logId)
    {
        $this->lastError = null;

        $accountId = isset($details['account_id']) ? $details['account_id'] : null;

        if (empty($accountId)) {
            return $this->giveUp('Account not found');
        }

        $accountDetails = Account::select(['id', 'details', 'platform'])->findOne(['account_id' => $accountId]);

        // Missed slots logged the accounts table's row id for a while instead
        // of the platform's id. Those rows are the ones most worth retrying, so
        // resolve them too rather than telling the user the account is gone.
        // Tried second, so a platform id always wins the match.
        if (empty($accountDetails)) {
            $accountDetails = Account::select(['id', 'details', 'platform'])->findOne(['id' => $accountId]);
        }

        if (empty($accountDetails)) {
            return $this->giveUp('Account not found');
        }

        $postId = isset($details['post_id']) ? $details['post_id'] : null;
        $post = $postId ? get_post($postId) : null;

        $scheduleDetail = Schedule::select(['id', 'config', 'schedule_type'])->findOne(['id' => $scheduleId]);

        if (empty($scheduleDetail)) {
            return $this->giveUp('Schedule not found');
        }

        $isPlatFormExists = $this->isPlatFormExists($accountDetails['id']);

        if (!$isPlatFormExists) {
            return $this->giveUp('Account not found or disabled');
        }

        $template = isset($scheduleDetail['config']['templates'][$accountDetails['platform']])
            ? $scheduleDetail['config']['templates'][$accountDetails['platform']]
            : null;

        if (empty($template)) {
            return $this->giveUp('Template not found for this platform');
        }

        $platformPostPublish = new $isPlatFormExists['class']();

        $postData = [
            'ID'   => $postId,
            'name' => $post->post_title ?? null,
        ];

        $this->lastError = null;

        return $platformPostPublish->publishPost([
            'post'            => $postData,
            'template'        => $template,
            'account_details' => $isPlatFormExists['details'],
            'schedule_id'     => $scheduleId,
            'schedule_type'   => $scheduleDetail['schedule_type'],
            'log_id'          => $logId,
            'retry'           => true,
        ]);
    }

    /**
     * @param string $reason
     *
     * @return false
     */
    private function giveUp($reason)
    {
        $this->lastError = $reason;

        return false;
    }
}
