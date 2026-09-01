<?php

namespace BitApps\Social\Utils;

use WP_Error;

/**
 * Turns a transport failure into something a site owner can act on.
 *
 * A WP_Error out of wp_remote_request means the site never reached the
 * provider at all - DNS, routing, a firewall, TLS. Passing its message
 * straight through puts "cURL error 28: SSL connection timeout" in front of
 * the user: the symptom in cURL's words, indistinguishable from a plugin
 * fault, so the ticket comes here instead of going to the host. Name whose
 * problem it is and keep the original text, which is what actually diagnoses
 * it.
 *
 * Pro carries an identical copy (BitApps\SocialPro\Utils\ConnectError) so a
 * newer Pro against an older Free still has a message to show — keep the
 * wording in step.
 */
final class ConnectError
{
    /**
     * @param mixed  $error    the WP_Error the HTTP client returned
     * @param string $platform display name of the network being reached
     *
     * @return string
     */
    public static function unreachable($error, $platform)
    {
        $reason = $error instanceof WP_Error ? $error->get_error_message() : (string) $error;

        return \sprintf(
            // translators: 1: social network name, 2: the underlying transport error
            __(
                'Your site could not reach %1$s (%2$s). This is a network problem on the server, not a problem with your %1$s account. Try again, and if it keeps happening ask your host to check the server\'s outbound HTTPS connections.',
                'bit-social'
            ),
            $platform,
            $reason
        );
    }
}
