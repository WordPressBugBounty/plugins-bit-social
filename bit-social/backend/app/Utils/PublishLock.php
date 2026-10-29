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
 * The lock also lets only one run of a schedule publish at a time. Two cron
 * requests can fire the same event at once (an external cron and a spawned
 * WP-Cron), and each run would publish a different post.
 *
 * An option rather than a transient: add_option claims it atomically, because
 * option_name carries a unique index. It stores the time it was taken, so a
 * lock left by a run that died half way through (fatal, request timeout)
 * expires after TTL and does not shield the schedule from the reaper forever.
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
     *
     * @return bool false when another run holds the lock
     */
    public static function acquire($scheduleId)
    {
        if (self::claim($scheduleId)) {
            return true;
        }

        if (self::isHeld($scheduleId)) {
            return false;
        }

        // The run that took the lock died without a release.
        delete_option(self::key($scheduleId));

        return self::claim($scheduleId);
    }

    /**
     * @param int $scheduleId
     */
    public static function release($scheduleId)
    {
        delete_option(self::key($scheduleId));
    }

    /**
     * @param int $scheduleId
     *
     * @return bool
     */
    public static function isHeld($scheduleId)
    {
        self::forgetCachedLock($scheduleId);

        $lockedAt = (int) get_option(self::key($scheduleId));

        return $lockedAt > 0 && ($lockedAt + self::TTL) > time();
    }

    /**
     * @param int $scheduleId
     *
     * @return bool
     */
    private static function claim($scheduleId)
    {
        // add_option skips its "already exists" check when the request cached
        // the option as missing, so read it again from the database.
        self::forgetCachedLock($scheduleId);

        return add_option(self::key($scheduleId), time(), '', 'no');
    }

    /**
     * Another request takes and releases the lock, so the copy cached in this
     * request can be out of date.
     *
     * @param int $scheduleId
     */
    private static function forgetCachedLock($scheduleId)
    {
        $key = self::key($scheduleId);

        wp_cache_delete($key, 'options');

        $notOptions = wp_cache_get('notoptions', 'options');

        if (\is_array($notOptions) && isset($notOptions[$key])) {
            unset($notOptions[$key]);
            wp_cache_set('notoptions', $notOptions, 'options');
        }
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
