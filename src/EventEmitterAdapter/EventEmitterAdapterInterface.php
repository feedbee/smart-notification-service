<?php

namespace Feedbee\SmartNotificationService\EventEmitterAdapter;

/**
 * Adapter interface for event manager implementations of different vendors
 */
interface EventEmitterAdapterInterface
{
    /**
     * Attach event handler to multiple events at once
     *
     * @param array|string $eventNames
     * @param callable $handler
     * @param object $eventEmitter
     */
    public function attachEvents($eventNames, callable $handler, $eventEmitter);
}