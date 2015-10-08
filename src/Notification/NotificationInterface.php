<?php

namespace Feedbee\SmartNotificationService\Notification;

use Feedbee\SmartNotificationService\Message\MessageInterface;

interface NotificationInterface
{
    /**
     * Get array of messages generated for current notification
     *
     * @return MessageInterface[]
     */
    public function getMessages();
}