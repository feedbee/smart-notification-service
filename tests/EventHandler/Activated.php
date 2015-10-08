<?php

namespace Feedbee\SmartNotificationService\Tests\EventHandler;

class Activated
{
    public function __invoke($event)
    {
        print "Activated: {$event->params['value']}\n";
    }
}