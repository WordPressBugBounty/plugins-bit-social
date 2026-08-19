<?php

namespace BitApps\Social\Utils;

/**
 * Resolves the "created by" attribution shown on the Logs, Schedules and Share
 * Now lists. Schedules/share-now store the creator's user id in their config
 * (see ScheduleController/ShareNowController); this turns it into a display
 * name + role label for the UI.
 */
class Creator
{
    /**
     * @param int|string $userId
     *
     * @return array{name: null|string, role: null|string}
     */
    public static function info($userId)
    {
        $result = ['name' => null, 'role' => null];

        $userId = absint($userId);

        if (!$userId) {
            return $result;
        }

        $user = get_userdata($userId);

        if (!$user) {
            return $result;
        }

        $result['name'] = $user->display_name;

        $roleSlug = $user->roles[0] ?? null;

        if ($roleSlug) {
            $roleNames = wp_roles()->get_names();
            $result['role'] = $roleNames[$roleSlug] ?? ucfirst($roleSlug);
        }

        return $result;
    }

    /**
     * The creator user id stored on a schedule/share-now config, or 0.
     *
     * @param mixed $config
     *
     * @return int
     */
    public static function idFromConfig($config)
    {
        $config = \is_string($config) ? json_decode($config, true) : (array) $config;

        return isset($config['created_by']) ? (int) $config['created_by'] : 0;
    }

    /**
     * Stamps created_by_name / created_by_role on a schedule/share-now row so it
     * serializes to the frontend list.
     *
     * @param mixed $schedule
     */
    public static function attachToSchedule($schedule)
    {
        $info = self::info(self::idFromConfig($schedule->config));

        $schedule->created_by_name = $info['name'];
        $schedule->created_by_role = $info['role'];

        return $schedule;
    }
}
