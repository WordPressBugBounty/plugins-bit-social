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

        if (isset($config['created_by'])) {
            return (int) $config['created_by'];
        }

        // Delayed auto-post rows carried no creator until AutoPostService started
        // stamping one, and every one of them read "Unknown". They target a single
        // post, and whoever published it is the answer — so read it back rather
        // than leaving old rows blank forever.
        $postIds = isset($config['post_filters']['specific_postIds'])
            ? array_values((array) $config['post_filters']['specific_postIds'])
            : [];

        if (\count($postIds) !== 1) {
            return 0;
        }

        $post = get_post((int) $postIds[0]);

        return $post ? (int) $post->post_author : 0;
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
