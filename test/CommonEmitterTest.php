<?php

namespace Feedbee\SmartNotificationService\Test;

class CommonEmitterTest extends BasicTest
{
    public function run()
    {
        parent::run(new EventEmitter\Custom, 'Feedbee\SmartNotificationService\EventEmitterAdapter\Common');
    }
}