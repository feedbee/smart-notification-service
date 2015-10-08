<?php

namespace Feedbee\SmartNotificationService\Tests\EventHandler;

use Feedbee\SmartNotificationService\EventHandler\AbstractEventHandler;
use Feedbee\SmartNotificationService\Tests\Notification;

class Activated extends AbstractEventHandler
{
    public function __invoke($event)
    {
        print "Handler called for event `Activated` with value `{$event->params['value']}`\n";

        $notifications = [
            new Notification\ActivatedToA($event->params['value']),
            new Notification\ActivatedToB($event->params['value']),
        ];

        $this->sendNotifications($notifications);
    }
}