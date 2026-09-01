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

    /**
     * Key inside `details` marking an account whose grant can no longer be
     * renewed. See flagNeedsReconnect().
     */
    public const RECONNECT_FLAG = 'needs_reconnect';

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

        // A successful (re)connect replaces the grant, so any stale
        // "needs reconnect" warning must not survive it.
        if (\array_key_exists('details', $data)) {
            $data['details'] = self::withoutReconnectFlag($data['details']);
        }

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
     * Flag an account as needing a manual reconnect. Set when its stored grant
     * can no longer be renewed — the refresh token expired, the user revoked
     * the app, or the app credentials changed — so every publish from now on
     * fails until the account is re-authorized. The Accounts screen reads this
     * off `details` to warn on the card; nothing else acts on it, so a false
     * positive costs a warning, never a skipped post.
     *
     * @param string $platform
     * @param string $accountId
     * @param string $message   short, user-facing cause
     *
     * @return void
     */
    public static function flagNeedsReconnect($platform, $accountId, $message = '')
    {
        if (empty($platform) || empty($accountId)) {
            return;
        }

        $account = self::findOne(['account_id' => $accountId, 'platform' => $platform]);

        if (!$account || empty($account->details)) {
            return;
        }

        $details = \is_string($account->details) ? json_decode($account->details) : $account->details;

        if (!\is_object($details)) {
            return;
        }

        // Keep the first failure's timestamp: it tells the user how long the
        // account has been broken, and rewriting the row on every retry would
        // reset that.
        if (!empty($details->{self::RECONNECT_FLAG}->at)) {
            return;
        }

        $details->{self::RECONNECT_FLAG} = (object) [
            'at'      => time(),
            'message' => (string) $message,
        ];

        $account->update(['details' => $details])->save();
    }

    /**
     * Drop the flag from a details object that is about to be saved. Callers
     * that already hold the row (the refresh services, which write the renewed
     * token) use this instead of a second query.
     *
     * @param mixed $details object, array or JSON string; returned in kind
     *
     * @return mixed
     */
    public static function withoutReconnectFlag($details)
    {
        if (\is_string($details)) {
            $decoded = json_decode($details);

            if (!\is_object($decoded)) {
                return $details;
            }

            unset($decoded->{self::RECONNECT_FLAG});

            return wp_json_encode($decoded);
        }

        if (\is_object($details)) {
            unset($details->{self::RECONNECT_FLAG});

            return $details;
        }

        if (\is_array($details)) {
            unset($details[self::RECONNECT_FLAG]);

            return $details;
        }

        return $details;
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
