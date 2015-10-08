<?php

namespace Feedbee\SmartNotificationService;

/**
 * Service to tie events emitters and consumers. Attaches events-listeners given in Event Handles Map to events-emitter
 * objects. Different kinds of events-emitter objects are supported through events emitter adapters. So this service can
 * be used in conjunction as with observable objects, as with different repositories, service locators, IoC containers
 * and so on.
 */
class EventHandlerService
{
    /**
     * Map handlers to events
     *
     * [
     *   [
     *     'events-emitter' => $eventsEmitter, // optional
     *     'events-emitter-adapter' => $eventsEmitterAdapter, // optional
     *     'events' => ['event-name-1', 'event-name-2', ...],
     *     'handlers' => [$handler_1, $handler_2, ...],
     *     'exceptions-mode' => 'intercept' | 'bypath', // optional
     *   ],
     *   ...
     * ]
     *
     * $eventsEmitter and $handler_* may be both object instances (any type of callable in case of $handler_*)
     * or a name of a service to get it from IoC container.
     *
     * $eventsEmitter may be either event dispatcher/manager or observable object depending on what way of
     * event processing you use. If is not set, $this->defaultEventsEmitter is used.
     *
     * $eventsEmitterAdapter is EventsEmitterAdapter\EventsEmitterAdapterInterface implementation that call
     * corresponding event attach method for event manager implementations of different vendors. If is not set,
     * $this->defaultEventsEmitterAdapter is used.
     *
     * @var array
     */
    protected $eventHandlersMap = [];

    /**
     * @var object
     */
    private $defaultEventsEmitter = null;

    /**
     * @var EventsEmitterAdapter\EventsEmitterAdapterInterface
     */
    private $defaultEventsEmitterAdapter = null;

    /**
     * @var Resolver\ResolverInterface
     */
    private $eventsEmitterResolver;

    /**
     * @var Resolver\ResolverInterface
     */
    private $eventsEmitterAdapterResolver;

    /**
     * @var Resolver\ResolverInterface
     */
    private $handlerResolver;

    /**
     * @var DeliveryService
     */
    private $deliveryService;

    /**
     * Get whole Event Handlers Map
     *
     * @return array
     */
    public function getEventHandlersMap()
    {
        return $this->eventHandlersMap;
    }

    /**
     * Set whole Event Handlers Map
     *
     * @param array $eventHandlersMap
     */
    public function setEventHandlersMap(array $eventHandlersMap)
    {
        $this->eventHandlersMap = $eventHandlersMap;
    }

    /**
     * Add element to Event Handlers Map
     *
     * @param array $eventNames
     * @param array $handlers
     * @param string $exceptionsMode
     * @param object $eventsEmitter
     * @param EventsEmitterAdapter\EventsEmitterAdapterInterface $eventsEmitterAdapter
     */
    public function addEventHandlers(array $eventNames, array $handlers, $exceptionsMode = 'bypath',
                                     $eventsEmitter = null,
                                     EventsEmitterAdapter\EventsEmitterAdapterInterface $eventsEmitterAdapter = null)
    {
        $this->eventHandlersMap[] = [
            'events-emitter' => $eventsEmitter,
            'events-emitter-adapter' => $eventsEmitterAdapter,
            'events' => $eventNames,
            'handlers' => $handlers,
            'exceptions-mode' => $exceptionsMode,
        ];
    }

    /**
     * Attach events corresponding to Event Handlers Map
     *
     * @throws \Exception
     */
    public function attachEvents()
    {
        foreach ($this->eventHandlersMap as $element) {
           $this->processEventHandlerMapElement($element);
        }
    }

    /**
     * Attach events from Event Handlers Map element
     *
     * @param array $element
     * @throws \Exception
     */
    private function processEventHandlerMapElement(array $element)
    {
        $eventsEmitter = isset($element['events-emitter']) ? $element['events-emitter'] : $this->defaultEventsEmitter;
        if (!isset($eventsEmitter)) {
            throw new \RuntimeException('Event emitter is not set');
        }
        $eventsEmitter = $this->resolveEventsEmitter($eventsEmitter);

        $eventsEmitterAdapter = isset($element['events-emitter-adapter'])
            ? $element['events-emitter-adapter'] : $this->defaultEventsEmitterAdapter;
        if (!isset($eventsEmitterAdapter)) {
            throw new \RuntimeException('Event emitter adapter is not set');
        }
        $eventsEmitterAdapter = $this->resolveEventsEmitterAdapter($eventsEmitterAdapter);

        if (!isset($element['handlers'])) {
            throw new \RuntimeException('Event handler is not set');
        }
        $handlers = self::makeArrayAnyway($element['handlers']);

        if (!isset($element['events'])) {
            throw new \RuntimeException('Events is not set');
        }
        $eventNames = self::makeArrayAnyway($element['events']);

        $interceptExceptions = isset($element['exceptions-mode']) && $element['exceptions-mode'] == 'intercept';

        foreach ($handlers as $handler) {
            $handler = $this->setupHandler($handler);
            $this->attachEventsItem($eventsEmitter, $eventsEmitterAdapter, $eventNames, $handler, $interceptExceptions);
        }
    }

    /**
     * Resolve handler now or create resolver function depending on Lazy Loading option
     *
     * @param mixed $handler
     * @return callable
     */
    private function setupHandler($handler)
    {
        $handlerLazyLoading = true; // make customizable later
        if ($handlerLazyLoading) {
            $handler = function () use ($handler) {
                $realHandler = $this->resolveHandler($handler);
                if (!is_callable($realHandler)) {
                    throw new \RuntimeException('Event handler must be callable, but `' . get_class($realHandler) . '` given');
                }
                call_user_func_array($realHandler, func_get_args());
            };
        } else {
            $handler = $this->resolveHandler($handler);
            $handler->setDeliveryService($this->resolveDeliveryService());
            if (!is_callable($handler)) {
                throw new \RuntimeException('Event handler must be callable, but `' . get_class($handler) . '` given');
            }
        }

        return $handler;
    }

    /**
     * @param object $eventsEmitter
     * @param EventsEmitterAdapter\EventsEmitterAdapterInterface $eventsEmitterAdapter
     * @param array $eventNames
     * @param callable $handler
     * @param bool $interceptExceptions
     * @throws \Exception
     */
    private function attachEventsItem($eventsEmitter, EventsEmitterAdapter\EventsEmitterAdapterInterface $eventsEmitterAdapter,
                                      $eventNames, callable $handler, $interceptExceptions)
    {
        try {
            $eventsEmitterAdapter->attachEvents($eventNames, $handler, $eventsEmitter);
        } catch (\Exception $e) {
            if (!$interceptExceptions) {
                throw $e;
            }
            // else do nothing
        }
    }

    private static function makeArrayAnyway($data)
    {
        if (!is_array($data)) {
            return [$data];
        }

        return $data;
    }

    /**
     * @param mixed $nameOrInstance
     * @return object
     */
    private function resolveEventsEmitter($nameOrInstance)
    {
        if (is_object($nameOrInstance)) {
            return $nameOrInstance;
        }

        try {
            return $this->getEventsEmitterResolver()->resolve($nameOrInstance);
        }
        catch (\RuntimeException $e)
        {
            throw new \RuntimeException("Events emitter `$nameOrInstance` not found");
        }
    }

    /**
     * @param mixed $nameOrInstance
     * @return EventsEmitterAdapter\EventsEmitterAdapterInterface
     */
    private function resolveEventsEmitterAdapter($nameOrInstance)
    {
        if (is_object($nameOrInstance)) {
            return $nameOrInstance;
        }

        try {
            return $this->getEventsEmitterAdapterResolver()->resolve($nameOrInstance);
        }
        catch (\RuntimeException $e)
        {
            throw new \RuntimeException("Events emitter adapter `$nameOrInstance` not found");
        }
    }

    /**
     * @param mixed $nameOrInstance
     * @return EventHandler\EventHandlerInterface
     */
    private function resolveHandler($nameOrInstance)
    {
        if (is_object($nameOrInstance)) {
            return $nameOrInstance;
        }

        try {
            return $this->getHandlerResolver()->resolve($nameOrInstance);
        }
        catch (\RuntimeException $e)
        {
            throw new \RuntimeException("Handler `$nameOrInstance` not found");
        }
    }

    /**
     * Overload to use custom DeliveryService implementation
     *
     * @return DeliveryService
     */
    private function resolveDeliveryService()
    {
        if (is_null($this->getDeliveryService())) {
            $this->setDeliveryService(new DeliveryService());
        }

        return $this->getDeliveryService();
    }

    /**
     * @return DeliveryService
     */
    protected function getDeliveryService()
    {
        return $this->deliveryService;
    }

    /**
     * @param DeliveryService $deliveryService
     */
    protected function setDeliveryService($deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    /**
     * @return object
     */
    public function getDefaultEventsEmitter()
    {
        return $this->defaultEventsEmitter;
    }

    /**
     * @param object $defaultEventsEmitter
     */
    public function setDefaultEventsEmitter($defaultEventsEmitter = null)
    {
        $this->defaultEventsEmitter = $defaultEventsEmitter;
    }

    /**
     * @return EventsEmitterAdapter\EventsEmitterAdapterInterface
     */
    public function getDefaultEventsEmitterAdapter()
    {
        return $this->defaultEventsEmitterAdapter;
    }

    /**
     * @param EventsEmitterAdapter\EventsEmitterAdapterInterface $defaultEventsEmitterAdapter
     */
    public function setDefaultEventsEmitterAdapter(EventsEmitterAdapter\EventsEmitterAdapterInterface $defaultEventsEmitterAdapter = null)
    {
        $this->defaultEventsEmitterAdapter = $defaultEventsEmitterAdapter;
    }

    /**
     * @return Resolver\ResolverInterface
     */
    public function getEventsEmitterResolver()
    {
        return $this->eventsEmitterResolver;
    }

    /**
     * @param Resolver\ResolverInterface $eventsEmitterResolver
     */
    public function setEventsEmitterResolver($eventsEmitterResolver)
    {
        $this->eventsEmitterResolver = $eventsEmitterResolver;
    }

    /**
     * @return Resolver\ResolverInterface
     */
    public function getEventsEmitterAdapterResolver()
    {
        return $this->eventsEmitterAdapterResolver;
    }

    /**
     * @param Resolver\ResolverInterface $eventsEmitterAdapterResolver
     */
    public function setEventsEmitterAdapterResolver($eventsEmitterAdapterResolver)
    {
        $this->eventsEmitterAdapterResolver = $eventsEmitterAdapterResolver;
    }

    /**
     * @return Resolver\ResolverInterface
     */
    public function getHandlerResolver()
    {
        return $this->handlerResolver;
    }

    /**
     * @param Resolver\ResolverInterface $handlerResolver
     */
    public function setHandlerResolver($handlerResolver)
    {
        $this->handlerResolver = $handlerResolver;
    }
}