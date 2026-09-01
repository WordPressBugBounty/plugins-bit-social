<?php

namespace BitApps\Social\Model;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPDatabase\Model;

class Log extends Model
{
    public const status = [
        'FAIL'    => 0,
        'SUCCESS' => 1,
    ];

    /**
     * Stored in details.reason to tell apart the two ways a post fails to go
     * out: a platform rejecting it, and a slot passing without a publish
     * attempt at all (WP-Cron caught up too late — see
     * ScheduleInfo::isExecutionStale()).
     *
     * Kept in details rather than as a third status value because `status` is a
     * boolean column; widening it would mean a migration and would break every
     * existing `status = 0/1` read.
     */
    /**
     * What produced a run. A log entry means little without it: "failed" reads
     * very differently for a scheduled campaign, a delayed auto-post and a
     * Share Now the user fired by hand a minute ago.
     *
     * AUTO_POST matches CustomSchedule::AUTO_POST_SOURCE, which is what the
     * schedule row itself is tagged with.
     */
    public const source = [
        'AUTO_POST' => 'auto_post',
        'SCHEDULE'  => 'schedule',
        'SHARE_NOW' => 'share_now',
    ];

    public const reason = [
        'MISSED' => 'missed',
    ];

    protected $fillable = [
        'schedule_id',
        'platform',
        'details',
        'status',
        'run_id',
    ];

    protected $prefix = Config::VAR_PREFIX;

    protected $casts = [
        'details' => 'object',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'id', 'schedule_id');
    }
}
