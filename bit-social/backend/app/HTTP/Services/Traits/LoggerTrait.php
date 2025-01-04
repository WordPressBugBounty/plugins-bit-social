<?php

namespace BitApps\Social\HTTP\Services\Traits;

use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\Model\Log;

trait LoggerTrait
{
    public function logCreate($data)
    {
        $scheduleId = \array_key_exists('schedule_id', $data) ? $data['schedule_id'] : null;
        $status = \array_key_exists('status', $data) ? $data['status'] : 1;

        return Log::insert([
            'schedule_id' => $scheduleId,
            'details'     => $data['details'],
            'platform'    => $data['platform'],
            'status'      => $status,
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
