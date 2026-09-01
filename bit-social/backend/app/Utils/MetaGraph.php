<?php

namespace BitApps\Social\Utils;

/**
 * The Meta Graph API version every Facebook and Instagram call is pinned to.
 *
 * Meta retires a version about two years after its release and then stops
 * serving it. Services here were still pinned to v16.0 (2023), so token
 * renewal for Facebook and legacy Instagram accounts started failing against
 * a version Meta no longer answers - connections quietly aged out and posting
 * stopped with no change on the site's side. Keeping one constant makes the
 * next bump a one-line change instead of a hunt through the services.
 *
 * Pro keeps its own copy (BitApps\SocialPro\Utils\MetaGraph) because the two
 * plugins are updated independently and this constant is read as a property
 * default — bump both together.
 */
final class MetaGraph
{
    public const VERSION = 'v23.0';
}
