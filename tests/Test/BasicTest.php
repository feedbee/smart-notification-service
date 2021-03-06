<?php

namespace Feedbee\SmartNotificationService\Tests\Test;

use Feedbee\SmartNotificationService\Service\EventHandlerService;
use Feedbee\SmartNotificationService\Service\DeliveryService;
use Feedbee\SmartNotificationService\Resolver\Resolver;
use Feedbee\SmartNotificationService\Tests\EventEmitter;
use Feedbee\SmartNotificationService\Tests\Helper\Repo;

class BasicTest
{

    /**
     * @param EventEmitter\EventEmitterInterface $emitter
     * @param mixed $emitterAdapter
     */
    public function run($emitter, $emitterAdapter)
    {
        // Setup resolvers

        $commonResolver = new Resolver;
        $commonResolver->setFactory(function ($name) {
            return new $name;
        });

        $eventEmitterResolver = new Resolver;
        $repository = new Repo;
        $repository->set('emitter', $emitter);
        $eventEmitterResolver->setLocator($repository);

        // Setup delivery service
        $ds = new DeliveryService([
            'test' => 'Feedbee\SmartNotificationService\DeliveryChannel\Test\EchoChannel'
        ], $commonResolver);

        // Setup handler service

        $hs = new EventHandlerService;
        $hs->setEventEmitterResolver($eventEmitterResolver);
        $hs->setEventEmitterAdapterResolver($commonResolver);
        $hs->setEventHandlerResolver($commonResolver);
        $hs->setDeliveryService($ds);
        $handlers = [
            [
                'events-emitter' => 'emitter',
                'events-emitter-adapter' => $emitterAdapter,
                'events' => EventEmitter\Custom::EVENT_ACTIVATED,
                'handlers' => 'Feedbee\SmartNotificationService\Tests\EventHandler\Activated'
            ]
        ];

        // Attach events

        $hs->attachEvents($handlers);


        // Trigger event

        $emitter->activate('X');
    }
}