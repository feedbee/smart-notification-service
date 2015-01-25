<?php

namespace Feedbee\SmartNotificationService\EventsEmitterAdapter;

/**
 * Adapter interface for event manager implementations of different vendors
 */
interface EventsEmitterAdapterInterface
{
    /**
     * Attach event handler to multiple events at once
     *
     * @param array $eventNames
     * @param callable $handler
     * @param object $eventsEmitter
     */
    public function attachEvents(array $eventNames, callable $handler, $eventsEmitter);
}