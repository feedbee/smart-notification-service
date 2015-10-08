<?php

namespace Feedbee\SmartNotificationService\Tests\Test;

use Feedbee\SmartNotificationService\Tests\EventEmitter;

class CommonEmitterTest extends BasicTest
{
    public function run()
    {
        parent::run(new EventEmitter\Custom, 'Feedbee\SmartNotificationService\EventEmitterAdapter\Common');
    }
}