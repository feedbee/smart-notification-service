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
     *     'events-emitter' => $eventEmitter, // optional
     *     'events-emitter-adapter' => $eventEmitterAdapter, // optional
     *     'events' => ['event-name-1', 'event-name-2', ...],
     *     'handlers' => [$handler_1, $handler_2, ...],
     *     'exceptions-mode' => 'intercept' | 'bypath', // optional
     *   ],
     *   ...
     * ]
     *
     * $eventEmitter and $handler_* may be both object instances (any type of callable in case of $handler_*)
     * or a name of a service to get it from IoC container.
     *
     * $eventEmitter may be either event dispatcher/manager or observable object depending on what way of
     * event processing you use. If is not set, $this->defaultEventEmitter is used.
     *
     * $eventEmitterAdapter is EventEmitterAdapter\EventEmitterAdapterInterface implementation that call
     * corresponding event attach method for event manager implementations of different vendors. If is not set,
     * $this->defaultEventEmitterAdapter is used.
     *
     * @var array
     */
    protected $eventHandlersMap = [];

    /**
     * @var object
     */
    private $defaultEventEmitter = null;

    /**
     * @var EventEmitterAdapter\EventEmitterAdapterInterface
     */
    private $defaultEventEmitterAdapter = null;

    /**
     * @var Resolver\ResolverInterface
     */
    private $eventEmitterResolver;

    /**
     * @var Resolver\ResolverInterface
     */
    private $eventEmitterAdapterResolver;

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
     * @param object $eventEmitter
     * @param EventEmitterAdapter\EventEmitterAdapterInterface $eventEmitterAdapter
     */
    public function addEventHandlers(array $eventNames, array $handlers, $exceptionsMode = 'bypath',
                                     $eventEmitter = null,
                                     EventEmitterAdapter\EventEmitterAdapterInterface $eventEmitterAdapter = null)
    {
        $this->eventHandlersMap[] = [
            'events-emitter' => $eventEmitter,
            'events-emitter-adapter' => $eventEmitterAdapter,
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
        $eventEmitter = isset($element['events-emitter']) ? $element['events-emitter'] : $this->defaultEventEmitter;
        if (!isset($eventEmitter)) {
            throw new \RuntimeException('Event emitter is not set');
        }
        $eventEmitter = $this->resolveEventEmitter($eventEmitter);

        $eventEmitterAdapter = isset($element['events-emitter-adapter'])
            ? $element['events-emitter-adapter'] : $this->defaultEventEmitterAdapter;
        if (!isset($eventEmitterAdapter)) {
            throw new \RuntimeException('Event emitter adapter is not set');
        }
        $eventEmitterAdapter = $this->resolveEventEmitterAdapter($eventEmitterAdapter);

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
            $this->attachEventsItem($eventEmitter, $eventEmitterAdapter, $eventNames, $handler, $interceptExceptions);
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
     * @param object $eventEmitter
     * @param EventEmitterAdapter\EventEmitterAdapterInterface $eventEmitterAdapter
     * @param array $eventNames
     * @param callable $handler
     * @param bool $interceptExceptions
     * @throws \Exception
     */
    private function attachEventsItem($eventEmitter, EventEmitterAdapter\EventEmitterAdapterInterface $eventEmitterAdapter,
                                      $eventNames, callable $handler, $interceptExceptions)
    {
        try {
            $eventEmitterAdapter->attachEvents($eventNames, $handler, $eventEmitter);
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
    private function resolveEventEmitter($nameOrInstance)
    {
        if (is_object($nameOrInstance)) {
            return $nameOrInstance;
        }

        try {
            return $this->getEventEmitterResolver()->resolve($nameOrInstance);
        }
        catch (\RuntimeException $e)
        {
            throw new \RuntimeException("Events emitter `$nameOrInstance` not found");
        }
    }

    /**
     * @param mixed $nameOrInstance
     * @return EventEmitterAdapter\EventEmitterAdapterInterface
     */
    private function resolveEventEmitterAdapter($nameOrInstance)
    {
        if (is_object($nameOrInstance)) {
            return $nameOrInstance;
        }

        try {
            return $this->getEventEmitterAdapterResolver()->resolve($nameOrInstance);
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
    public function getDefaultEventEmitter()
    {
        return $this->defaultEventEmitter;
    }

    /**
     * @param object $defaultEventEmitter
     */
    public function setDefaultEventEmitter($defaultEventEmitter = null)
    {
        $this->defaultEventEmitter = $defaultEventEmitter;
    }

    /**
     * @return EventEmitterAdapter\EventEmitterAdapterInterface
     */
    public function getDefaultEventEmitterAdapter()
    {
        return $this->defaultEventEmitterAdapter;
    }

    /**
     * @param EventEmitterAdapter\EventEmitterAdapterInterface $defaultEventEmitterAdapter
     */
    public function setDefaultEventEmitterAdapter(EventEmitterAdapter\EventEmitterAdapterInterface $defaultEventEmitterAdapter = null)
    {
        $this->defaultEventEmitterAdapter = $defaultEventEmitterAdapter;
    }

    /**
     * @return Resolver\ResolverInterface
     */
    public function getEventEmitterResolver()
    {
        return $this->eventEmitterResolver;
    }

    /**
     * @param Resolver\ResolverInterface $eventEmitterResolver
     */
    public function setEventEmitterResolver($eventEmitterResolver)
    {
        $this->eventEmitterResolver = $eventEmitterResolver;
    }

    /**
     * @return Resolver\ResolverInterface
     */
    public function getEventEmitterAdapterResolver()
    {
        return $this->eventEmitterAdapterResolver;
    }

    /**
     * @param Resolver\ResolverInterface $eventEmitterAdapterResolver
     */
    public function setEventEmitterAdapterResolver($eventEmitterAdapterResolver)
    {
        $this->eventEmitterAdapterResolver = $eventEmitterAdapterResolver;
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