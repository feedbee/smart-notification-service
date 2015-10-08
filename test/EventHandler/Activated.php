<?php

namespace Feedbee\SmartNotificationService\Test\EventHandler;

class Activated
{
    public function __invoke($event)
    {
        print "Activated: {$event->params['value']}\n";
    }
}