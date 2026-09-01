<?php

namespace BitApps\Social\Utils;

use BitApps\Social\Config;

/**
 * Marks a schedule as "publishing right now".
 *
 * The missed-post reaper runs at plugins_loaded, on requests that arrive while
 * a cron run is still working. To it, a schedule whose event has already been
 * consumed looks exactly like one whose event never fired — so a slow run (a
 * video upload, ten accounts, a platform taking its time) would be written off
 * as MISSED, logged and emailed as such, while it is in fact publishing.
 *
 * A transient rather than a column on the schedule: it has to expire on its own
 * if the run dies half way through (fatal, request timeout), or the schedule
 * would be shielded from the reaper forever.
 */
final class PublishLock
{
    /**
     * Long enough for the slowest realistic run, short enough that a run killed
     * mid-flight stops hiding its schedule within the hour.
     */
    public const TTL = 30 * MINUTE_IN_SECONDS;

    /**
     * @param int $scheduleId
     */
    public static function acquire($scheduleId)
    {
        set_transient(self::key($scheduleId), time(), self::TTL);
    }

    /**
     * @param int $scheduleId
     */
    public static function release($scheduleId)
    {
        delete_transient(self::key($scheduleId));
    }

    /**
     * @param int $scheduleId
     *
     * @return bool
     */
    public static function isHeld($scheduleId)
    {
        return (bool) get_transient(self::key($scheduleId));
    }

    /**
     * @param int $scheduleId
     *
     * @return string
     */
    private static function key($scheduleId)
    {
        return Config::VAR_PREFIX . 'publishing_' . absint($scheduleId);
    }
}
