<?php

namespace Feedbee\SmartNotificationService\EventsEmitterAdapter\Zf2;

use Feedbee\SmartNotificationService\EventsEmitterAdapter\EventsEmitterAdapterInterface,
    /** @noinspection PhpUndefinedNamespaceInspection */
    /** @noinspection PhpUndefinedClassInspection */
    Zend\EventManager\SharedEventManagerInterface as ZendSharedEventManagerInterface;

/**
 * Adapter implementation for ZF2 SharedEventManager
 * Zend\EventManager\SharedEventManagerInterface
 * http://framework.zend.com/manual/current/en/modules/zend.event-manager.event-manager.html
 */
class SharedEventManager implements EventsEmitterAdapterInterface
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Attach event handler to multiple events at once
     *
     * @param array|string $eventNames
     * @param callable $handler
     * @param ZendSharedEventManagerInterface $eventsEmitter
     */
    public function attachEvents($eventNames, callable $handler, $eventsEmitter)
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (!$eventsEmitter instanceof ZendEventManagerInterface) {
            throw new \RuntimeException('$eventsEmitter parameter for Zf2\EventManager adapter must be instance of '
                . '`Zend\EventManager\SharedEventManagerInterface`, but instance of `' . get_class($eventsEmitter)
                . '` was given');
        }

        if (!is_array($eventNames)) {
            $eventNames = [$eventNames];
        }

        foreach ($eventNames as $eventName) {
            list($namespace, $localEventName) = $this->splitEventName($eventName);
            /** @noinspection PhpUndefinedMethodInspection */
            $eventsEmitter->attach($namespace, $localEventName, $handler);
        }
    }

    /**
     * Split full event name (in format namespace.name) to two parts: namespace and local event name.
     * Returns it indexed array (0 for namespace, 1 for local event name).
     * Override this method to use another splitter.
     *
     * @param $fullName
     * @return array
     */
    protected function splitEventName($fullName)
    {
        return explode('.', $fullName, 2);
    }
}