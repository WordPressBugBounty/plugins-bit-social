<?php

namespace BitApps\Social\Model;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPDatabase\Model;

class Account extends Model
{
    public const accountType = [
        'DEFAULT'     => 1,
        'CUSTOM'      => 2,
        'AI_PLATFORM' => 3,
    ];

    public const ACCOUNT_STATUS = [
        'active'   => 1,
        'inactive' => 0,
    ];

    protected $prefix = Config::VAR_PREFIX;

    protected $fillable = [
        'profile_id',
        'account_id',
        'account_name',
        'details',
        'platform',
        'account_type',
        'status',
    ];

    protected $casts = [
        'id'           => 'int',
        'account_type' => 'int',
        'status'       => 'int',
        'details'      => 'object'
    ];

    /**
     * Insert-or-refresh a connected account. Reconnecting an account whose
     * platform access was revoked must UPDATE the existing row (matched by
     * platform + account_id) so its DB id — referenced by schedules,
     * auto-post settings, groups and pro account permissions — is preserved
     * instead of creating a duplicate row with the fresh tokens. The user's
     * enable/disable status choice is kept as-is.
     *
     * @param array $data
     *
     * @return mixed the saved model (update) or the insert response
     */
    public static function upsertConnected($data)
    {
        $existing = null;

        if (!empty($data['account_id']) && !empty($data['platform'])) {
            $existing = self::findOne([
                'account_id' => $data['account_id'],
                'platform'   => $data['platform'],
            ]);
        }

        if (!$existing) {
            return self::insert($data);
        }

        $existing->update([
            'profile_id'   => $data['profile_id'] ?? $existing->profile_id,
            'account_name' => $data['account_name'] ?? $existing->account_name,
            'account_type' => $data['account_type'] ?? $existing->account_type,
            'details'      => $data['details'] ?? $existing->details,
        ]);
        $existing->save();

        return $existing;
    }

    /**
     * upsertConnected for a batch of accounts (multi-account OAuth results —
     * e.g. Discord servers, Pinterest boards). True when at least one row
     * was saved.
     *
     * @param array $accounts
     *
     * @return bool
     */
    public static function upsertConnectedMany($accounts)
    {
        $saved = false;

        foreach ((array) $accounts as $account) {
            if (self::upsertConnected($account)) {
                $saved = true;
            }
        }

        return $saved;
    }
}
