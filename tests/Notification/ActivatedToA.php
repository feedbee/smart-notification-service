<?php

namespace Feedbee\SmartNotificationService\Tests\Notification;

use Feedbee\SmartNotificationService\Message\MessageInterface;
use Feedbee\SmartNotificationService\Message\TestMessage;
use Feedbee\SmartNotificationService\Notification\NotificationInterface;

class ActivatedToA implements NotificationInterface
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
            new TestMessage('A', "1:{$this->value}"),
            new TestMessage('A', "2:{$this->value}"),
        ];
    }
}