<?php

namespace Feedbee\SmartNotificationService\EventHandler;

interface EventHandlerInterface extends DeliveryServiceAwareInterface
{
    /**
     * @param object $event
     */
    public function __invoke($event);
}