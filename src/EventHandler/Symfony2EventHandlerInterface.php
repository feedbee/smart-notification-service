<?php

namespace Feedbee\SmartNotificationService\EventHandler;

use /** @noinspection PhpUndefinedNamespaceInspection */
    /** @noinspection PhpUndefinedClassInspection */
    Symfony\Component\EventDispatcher\Event as SymfonyEventInterface;

interface Symfony2EventHandlerInterface extends DeliveryServiceAwareInterface
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * @param SymfonyEventInterface $event
     */
    public function __invoke(/** @noinspection PhpUndefinedClassInspection */ SymfonyEventInterface $event);
}