<?php

namespace Feedbee\SmartNotificationService\Test\Handler;

class Activated
{
    public function __invoke($event)
    {
        print "Activated: {$event->params['value']}\n";
    }
}