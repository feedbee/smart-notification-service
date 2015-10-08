<?php

namespace Feedbee\SmartNotificationService\DeliveryChannel\Test;

use Feedbee\SmartNotificationService\Message\MessageInterface;
use Feedbee\SmartNotificationService\DeliveryChannel\DeliveryChannelInterface;
use Feedbee\SmartNotificationService\DeliveryChannel\Exception;

class EchoChannel implements DeliveryChannelInterface
{
    public function sendMessage(MessageInterface $message)
    {
        print "Got new message: {$message}\n";
    }
}