<?php

namespace Feedbee\SmartNotificationService\EventsEmitterAdapter;

use /** @noinspection PhpUndefinedNamespaceInspection */
    /** @noinspection PhpUndefinedClassInspection */
    Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

/**
 * Adapter implementation for Symfony2
 * Symfony\Component\EventDispatcher\EventDispatcher
 * http://symfony.com/doc/current/components/event_dispatcher/introduction.html#the-dispatcher
 */
class Symfony2 implements EventsEmitterAdapterInterface
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Attach event handler to multiple events at once
     *
     * @param array $eventNames
     * @param callable $handler
     * @param SymfonyEventDispatcher $eventsEmitter
     */
    public function attachEvents(array $eventNames, callable $handler, $eventsEmitter)
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (!$eventsEmitter instanceof SymfonyEventDispatcher) {
            throw new \RuntimeException('$eventsEmitter parameter for Symfony2 adapter must be instance of '
                . '`Symfony\Component\EventDispatcher\EventDispatcher`, but instance of `' . get_class($eventsEmitter)
                . '` was given');
        }

        foreach ($eventNames as $eventName) {
            /** @noinspection PhpUndefinedMethodInspection */
            $eventsEmitter->addListener($eventName, $handler);
        }
    }
}