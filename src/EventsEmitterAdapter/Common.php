<?php

namespace Feedbee\SmartNotificationService\EventsEmitterAdapter;

/**
 * Adapter implementation for custom event emitter, when it has attachListener($eventName, $handler) method
 */
class Common implements EventsEmitterAdapterInterface
{
    /**
     * Attach event handler to multiple events at once
     *
     * @param array|string $eventNames
     * @param callable $handler
     * @param object $eventsEmitter
     */
    public function attachEvents($eventNames, callable $handler, $eventsEmitter)
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (!method_exists($eventsEmitter, 'attachListener')) {
            throw new \RuntimeException('$eventsEmitter parameter for Simple adapter must have an'
                . ' attachListener($eventName, $handler) method implemented');
        }

        if (!is_array($eventNames)) {
            $eventNames = [$eventNames];
        }

        foreach ($eventNames as $eventName) {
            /** @noinspection PhpUndefinedMethodInspection */
            $eventsEmitter->attachListener($eventName, $handler);
        }
    }
}