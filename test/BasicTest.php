<?php

namespace Feedbee\SmartNotificationService\Test;

use Feedbee\SmartNotificationService\EventHandlerService;
use Feedbee\SmartNotificationService\Resolver\Resolver;

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

        $emitterResolver = new Resolver;


        $repository = new Repo;
        $repository->set('emitter', $emitter);
        $emitterResolver->setLocator($repository);

        // Setup handler service

        $hs = new EventHandlerService;
        $hs->setEventEmitterResolver($emitterResolver);
        $hs->setEventEmitterAdapterResolver($commonResolver);
        $hs->setHandlerResolver($commonResolver);
        $handlers = [
            [
                'events-emitter' => 'emitter',
                'events-emitter-adapter' => $emitterAdapter,
                'events' => EventEmitter\Custom::EVENT_ACTIVATED,
                'handlers' => 'Feedbee\SmartNotificationService\Test\EventHandler\Activated'
            ]
        ];

        // Attach events

        $hs->setEventHandlersMap($handlers);
        $hs->attachEvents();


        // Trigger event

        $emitter->activate('X');
    }
}