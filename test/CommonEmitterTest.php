<?php

namespace Feedbee\SmartNotificationService\Test;

class CommonEmitterTest extends BasicTest
{
    public function run()
    {
        parent::run(new Emitter\Custom, 'Feedbee\SmartNotificationService\EventsEmitterAdapter\Common');
    }
}