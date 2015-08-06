<?php

namespace Feedbee\SmartNotificationService\Test;

use Feedbee\SmartNotificationService\HandlerService;
use Feedbee\SmartNotificationService\ServiceResolver\ServiceResolver;

class BasicTest
{

    /**
     * @param Emitter\EmitterInterface $emitter
     * @param mixed $emitterAdapter
     */
    public function run($emitter, $emitterAdapter)
    {
        // Setup resolvers

        $commonResolver = new ServiceResolver;
        $commonResolver->setFactory(function ($name) {
            return new $name;
        });

        $emitterResolver = new ServiceResolver;


        $repository = new Repo;
        $repository->set('emitter', $emitter);
        $emitterResolver->setLocator($repository);

        // Setup handler service

        $hs = new HandlerService;
        $hs->setEventsEmitterResolver($emitterResolver);
        $hs->setEventsEmitterAdapterResolver($commonResolver);
        $hs->setHandlerResolver($commonResolver);
        $handlers = [
            [
                'events-emitter' => 'emitter',
                'events-emitter-adapter' => $emitterAdapter,
                'events' => Emitter\Custom::EVENT_ACTIVATED,
                'handlers' => 'Feedbee\SmartNotificationService\Test\Handler\Activated'
            ]
        ];

        // Attach events

        $hs->setEventHandlersMap($handlers);
        $hs->attachEvents();


        // Trigger event

        $emitter->activate('X');
    }
}