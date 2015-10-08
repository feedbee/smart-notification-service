<?php


namespace Feedbee\SmartNotificationService\EventEmitterAdapter\Zf2;


use Feedbee\SmartNotificationService\EventEmitterAdapter\EventEmitterAdapterInterface,
    /** @noinspection PhpUndefinedNamespaceInspection */
    /** @noinspection PhpUndefinedClassInspection */
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface;

/**
 * Adapter implementation for ZF2 EventManager
 * Zend\EventManager\EventManagerInterface
 * http://framework.zend.com/manual/current/en/modules/zend.event-manager.event-manager.html
 */
class EventManager implements EventEmitterAdapterInterface
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Attach event handler to multiple events at once
     *
     * @param array|string $eventNames
     * @param callable $handler
     * @param ZendEventManagerInterface $eventEmitter
     */
    public function attachEvents($eventNames, callable $handler, $eventEmitter)
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (!$eventEmitter instanceof ZendEventManagerInterface) {
            throw new \RuntimeException('$eventEmitter parameter for Zf2\EventManager adapter must be instance of '
                . '`Zend\EventManager\EventManagerInterface`, but instance of `' . get_class($eventEmitter)
                . '` was given');
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $eventEmitter->attach($eventNames, $handler);
    }
}