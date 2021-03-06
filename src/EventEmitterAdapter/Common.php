<?php

namespace Feedbee\SmartNotificationService\EventEmitterAdapter;

/**
 * Adapter implementation for custom event emitter, when it has attachListener($eventName, $handler) method
 */
class Common implements EventEmitterAdapterInterface
{
    /**
     * Attach event handler to multiple events at once
     *
     * @param array|string $eventNames
     * @param callable $handler
     * @param object $eventEmitter
     */
    public function attachEvents($eventNames, callable $handler, $eventEmitter)
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (!method_exists($eventEmitter, 'attachListener')) {
            throw new \RuntimeException('$eventEmitter parameter for Simple adapter must have an'
                . ' attachListener($eventName, $handler) method implemented');
        }

        if (!is_array($eventNames)) {
            $eventNames = [$eventNames];
        }

        foreach ($eventNames as $eventName) {
            /** @noinspection PhpUndefinedMethodInspection */
            $eventEmitter->attachListener($eventName, $handler);
        }
    }
}