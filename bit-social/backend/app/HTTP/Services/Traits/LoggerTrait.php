<?php

namespace BitApps\Social\HTTP\Services\Traits;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\Model\Log;
use BitApps\Social\Utils\LogRun;

trait LoggerTrait
{
    public function logCreate($data)
    {
        $scheduleId = \array_key_exists('schedule_id', $data) ? $data['schedule_id'] : null;
        $status = \array_key_exists('status', $data) ? $data['status'] : 1;
        $platform = $data['platform'];
        $details = $data['details'];

        Hooks::doAction(Config::withPrefix('log_data'), $scheduleId, $platform, $status, $details);

        return Log::insert([
            'schedule_id' => $scheduleId,
            'details'     => $details,
            'platform'    => $platform,
            'status'      => $status,
            // Ties this row to the other accounts of the same run so the Logs
            // screen can show one line instead of one per account.
            'run_id' => LogRun::current(),
        ]);
    }

    public function logUpdate($data, $id)
    {
        $log = Log::findOne(['id' => $id]);
        if (!$log) {
            return false;
        }
        $result = $log->update($data)->save();
        if ($result) {
            return Response::success(Log::findOne(['id' => $id]));
        }

        return Response::error('log update failed');
    }
}
