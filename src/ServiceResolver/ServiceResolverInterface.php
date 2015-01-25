<?php

namespace Feedbee\SmartNotificationService\ServiceResolver;

/**
 * Resolve resources creation based on it's name for needs of Smart Notification Service.
 */
interface ServiceResolverInterface
{
    public function resolve($nameOrInstance);
}