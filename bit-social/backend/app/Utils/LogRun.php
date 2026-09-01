<?php

namespace BitApps\Social\Utils;

/**
 * The id shared by every log row one publish run writes.
 *
 * A run fans out over the schedule's accounts and each account is logged by its
 * own platform service, so the id cannot be threaded through as an argument
 * without touching all fourteen of them. It lives here instead: SocialExecution
 * opens a run, everything logged until it closes is stamped with the same id,
 * and the Logs screen collapses those rows to one line.
 *
 * Deliberately not a property on LoggerTrait — a trait's static is per-using-
 * class, so the services would each see their own empty copy of it.
 */
final class LogRun
{
    /**
     * @var null|string
     */
    private static $currentId;

    /**
     * Opens a run. Nested calls keep the outermost id so a run stays one run.
     *
     * @return string
     */
    public static function start()
    {
        if (self::$currentId === null) {
            self::$currentId = self::generate();
        }

        return self::$currentId;
    }

    public static function end()
    {
        self::$currentId = null;
    }

    /**
     * Whether a run is already open.
     *
     * Lets a caller that may or may not be nested inside one (MissedRunLogger is
     * called both from inside SocialExecution's run and from the reaper, which
     * has none) close only the run it opened itself, instead of ending someone
     * else's halfway through.
     *
     * @return bool
     */
    public static function isOpen()
    {
        return self::$currentId !== null;
    }

    /**
     * The open run's id, or a fresh one when nothing is open — a log written
     * outside a run is a run of one, which keeps every row groupable and the
     * column free of nulls.
     *
     * @return string
     */
    public static function current()
    {
        return self::$currentId === null ? self::generate() : self::$currentId;
    }

    /**
     * @return string
     */
    private static function generate()
    {
        return md5(uniqid('bit_social_log_run', true));
    }
}
