<?php

namespace Feedbee\SmartNotificationService\Resolver;

/**
 * Resolve resources creation based on it's name for needs of Smart Notification Service.
 */
interface ResolverInterface
{
    public function resolve($nameOrInstance);
}