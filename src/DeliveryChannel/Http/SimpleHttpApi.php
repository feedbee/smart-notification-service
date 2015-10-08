<?php

namespace Feedbee\SmartNotificationService\DeliveryChannel\Http;

use Feedbee\SmartNotificationService\Message\MessageInterface;
use Feedbee\SmartNotificationService\DeliveryChannel\DeliveryChannelInterface;
use Feedbee\SmartNotificationService\DeliveryChannel\Exception;

abstract class SimpleHttpApi implements DeliveryChannelInterface
{
    abstract protected function getApiUrl(MessageInterface $message);

    public function sendMessage(MessageInterface $message)
    {
        $url = $this->getApiUrl($message);
        $result = file_get_contents($url);

        if ($result === false) {
            throw new Exception\DeliveryFailed;
        }
    }
}