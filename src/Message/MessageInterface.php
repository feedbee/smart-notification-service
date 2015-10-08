<?php

namespace Feedbee\SmartNotificationService\Message;

interface MessageInterface
{
    /**
     * String message type identifier for mapping on delivery channel
     *
     * @return string
     */
    public function getMessageType();

    public function __toString();
}