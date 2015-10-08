<?php

namespace Feedbee\SmartNotificationService\EventEmitterAdapter;

use /** @noinspection PhpUndefinedNamespaceInspection */
    /** @noinspection PhpUndefinedClassInspection */
    Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

/**
 * Adapter implementation for Symfony2
 * Symfony\Component\EventDispatcher\EventDispatcher
 * http://symfony.com/doc/current/components/event_dispatcher/introduction.html#the-dispatcher
 */
class Symfony2 implements EventEmitterAdapterInterface
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Attach event handler to multiple events at once
     *
     * @param array|string $eventNames
     * @param callable $handler
     * @param SymfonyEventDispatcher $eventEmitter
     */
    public function attachEvents($eventNames, callable $handler, $eventEmitter)
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (!$eventEmitter instanceof SymfonyEventDispatcher) {
            throw new \RuntimeException('$eventEmitter parameter for Symfony2 adapter must be instance of '
                . '`Symfony\Component\EventDispatcher\EventDispatcher`, but instance of `' . get_class($eventEmitter)
                . '` was given');
        }

        if (!is_array($eventNames)) {
            $eventNames = [$eventNames];
        }

        foreach ($eventNames as $eventName) {
            /** @noinspection PhpUndefinedMethodInspection */
            $eventEmitter->addListener($eventName, $handler);
        }
    }
}