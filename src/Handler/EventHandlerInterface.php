<?php

namespace Feedbee\SmartNotificationService\handler;

interface EventHandlerInterface extends DeliveryServiceAwareInterface
{
    /**
     * @param object $event
     */
    public function __invoke($event);
}