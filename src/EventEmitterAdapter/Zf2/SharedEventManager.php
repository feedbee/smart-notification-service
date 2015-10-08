<?php

namespace Feedbee\SmartNotificationService\EventEmitterAdapter\Zf2;

use Feedbee\SmartNotificationService\EventEmitterAdapter\EventEmitterAdapterInterface,
    /** @noinspection PhpUndefinedNamespaceInspection */
    /** @noinspection PhpUndefinedClassInspection */
    Zend\EventManager\SharedEventManagerInterface as ZendSharedEventManagerInterface;

/**
 * Adapter implementation for ZF2 SharedEventManager
 * Zend\EventManager\SharedEventManagerInterface
 * http://framework.zend.com/manual/current/en/modules/zend.event-manager.event-manager.html
 */
class SharedEventManager implements EventEmitterAdapterInterface
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Attach event handler to multiple events at once
     *
     * @param array|string $eventNames
     * @param callable $handler
     * @param ZendSharedEventManagerInterface $eventEmitter
     */
    public function attachEvents($eventNames, callable $handler, $eventEmitter)
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (!$eventEmitter instanceof ZendEventManagerInterface) {
            throw new \RuntimeException('$eventEmitter parameter for Zf2\EventManager adapter must be instance of '
                . '`Zend\EventManager\SharedEventManagerInterface`, but instance of `' . get_class($eventEmitter)
                . '` was given');
        }

        if (!is_array($eventNames)) {
            $eventNames = [$eventNames];
        }

        foreach ($eventNames as $eventName) {
            list($namespace, $localEventName) = $this->splitEventName($eventName);
            /** @noinspection PhpUndefinedMethodInspection */
            $eventEmitter->attach($namespace, $localEventName, $handler);
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