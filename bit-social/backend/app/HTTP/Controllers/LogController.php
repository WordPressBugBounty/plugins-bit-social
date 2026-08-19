<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\Model\Account;
use BitApps\Social\Model\Log;
use BitApps\Social\Utils\Creator;

final class LogController
{
    public function index(Request $request, $page, $limit)
    {
        $logs = Log::with('schedule');

        if ($request->date != 'all') {
            if ($dateRange = json_decode(stripslashes($request->date))) {
                $logs->whereBetween('DATE(`created_at`)', $dateRange[0], $dateRange[1]);
            }
        }
        if ($request->schedule != 'all' && isset($request->schedule)) {
            $logs->where('schedule_id', $request->schedule);
        }
        if ($request->status != 'all' && isset($request->status)) {
            $logs->where('status', $request->status);
        }
        if ($request->platform != 'all' && $request->platform) {
            $logs->where('platform', $request->platform);
        }

        $logs = $logs
            ->desc()
            ->paginate($page, $limit);

        if (isset($logs['data']) && \is_array($logs['data'])) {
            foreach ($logs['data'] as $log) {
                $this->attachCreator($log);
            }

            $this->attachAccountIcons($logs['data']);
        }

        return Response::success($logs);
    }

    public function destroy(Request $request)
    {
        Log::whereIn('id', $request->logIds)->delete();

        return Response::success('Selected logs deleted');
    }

    /**
     * Attaches each log's account avatar (account_icon) resolved from the
     * Account model by account_id. Batched into a single query for the whole
     * page so the Logs table can render account avatars without an N+1 lookup.
     * The image itself is a remote URL rendered (with a fallback) on the client.
     *
     * @param array $logs
     */
    private function attachAccountIcons(array $logs)
    {
        $accountIds = [];

        foreach ($logs as $log) {
            if (isset($log->details->account_id) && $log->details->account_id !== '') {
                $accountIds[] = (string) $log->details->account_id;
            }
        }

        $iconMap = [];

        if (!empty($accountIds)) {
            $accounts = Account::whereIn('account_id', array_values(array_unique($accountIds)))
                ->get(['account_id', 'details']);

            foreach ((array) $accounts as $account) {
                $iconMap[(string) $account->account_id] = $account->details->icon ?? null;
            }
        }

        foreach ($logs as $log) {
            $accountId = isset($log->details->account_id) ? (string) $log->details->account_id : '';
            $log->account_icon = $iconMap[$accountId] ?? null;
        }
    }

    /**
     * Stamps each log row with the creator's display name and role so the Logs
     * table can show who is responsible. Scheduled / share-now logs read the
     * creator recorded on the schedule config; auto-post logs (no schedule)
     * fall back to the published post's author.
     *
     * @param mixed $log
     */
    private function attachCreator($log)
    {
        $info = Creator::info($this->resolveCreatorId($log));

        $log->created_by_name = $info['name'];
        $log->created_by_role = $info['role'];
    }

    /**
     * @param mixed $log
     *
     * @return int
     */
    private function resolveCreatorId($log)
    {
        if (!empty($log->schedule)) {
            $config = $log->schedule->config;
            $config = \is_string($config) ? json_decode($config, true) : (array) $config;

            if (!empty($config['created_by'])) {
                return (int) $config['created_by'];
            }
        }

        // Auto-post logs carry no schedule — attribute to the post's author.
        $postId = isset($log->details->post_id) ? absint($log->details->post_id) : 0;

        if ($postId) {
            return (int) get_post_field('post_author', $postId);
        }

        return 0;
    }
}
