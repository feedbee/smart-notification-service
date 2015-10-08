<?php

namespace Feedbee\SmartNotificationService\Tests\Notification;

use Feedbee\SmartNotificationService\Message\MessageInterface;
use Feedbee\SmartNotificationService\Message\TestMessage;
use Feedbee\SmartNotificationService\Notification\NotificationInterface;

class ActivatedToB implements NotificationInterface
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get array of messages generated for current notification
     *
     * @return MessageInterface[]
     */
    public function getMessages()
    {
        return [
            new TestMessage('B', "1:{$this->value}"),
        ];
    }
}