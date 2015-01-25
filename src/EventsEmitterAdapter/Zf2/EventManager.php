<?php


namespace Feedbee\SmartNotificationService\EventsEmitterAdapter\Zf2;


use Feedbee\SmartNotificationService\EventsEmitterAdapter\EventsEmitterAdapterInterface,
    /** @noinspection PhpUndefinedNamespaceInspection */
    /** @noinspection PhpUndefinedClassInspection */
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface;

/**
 * Adapter implementation for ZF2 EventManager
 * Zend\EventManager\EventManagerInterface
 * http://framework.zend.com/manual/current/en/modules/zend.event-manager.event-manager.html
 */
class EventManager implements EventsEmitterAdapterInterface
{
    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Attach event handler to multiple events at once
     *
     * @param array $eventNames
     * @param callable $handler
     * @param ZendEventManagerInterface $eventsEmitter
     */
    public function attachEvents(array $eventNames, callable $handler, $eventsEmitter)
    {
        /** @noinspection PhpUndefinedClassInspection */
        if (!$eventsEmitter instanceof ZendEventManagerInterface) {
            throw new \RuntimeException('$eventsEmitter parameter for Zf2\EventManager adapter must be instance of '
                . '`Zend\EventManager\EventManagerInterface`, but instance of `' . get_class($eventsEmitter)
                . '` was given');
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $eventsEmitter->attach($eventNames, $handler);
    }
}