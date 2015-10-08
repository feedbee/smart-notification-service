<?php

namespace Feedbee\SmartNotificationService\Tests\EventHandler;

use Feedbee\SmartNotificationService\EventHandler\AbstractEventHandler;

class Activated extends AbstractEventHandler
{
    public function __invoke($event)
    {
        print "Activated: {$event->params['value']}\n";


    }
}