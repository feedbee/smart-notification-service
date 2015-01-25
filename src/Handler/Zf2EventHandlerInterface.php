<?php

namespace Feedbee\SmartNotificationService\handler;

use /** @noinspection PhpUndefinedNamespaceInspection */
    /** @noinspection PhpUndefinedClassInspection */
    Zend\EventManager\EventInterface as ZendEventInterface;

interface Zf2EventHandlerInterface extends DeliveryServiceAwareInterface
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * @param ZendEventInterface $event
     */
    public function __invoke(/** @noinspection PhpUndefinedClassInspection */ ZendEventInterface $event);
}