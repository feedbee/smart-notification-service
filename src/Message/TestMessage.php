<?php

namespace Feedbee\SmartNotificationService\Message;

/**
 * Test message type for testing and debugging purpose
 */
class TestMessage extends BasicMessage
{
    /**
     * @param string $receiver
     * @param string $message
     */
    public function __construct($receiver, $message)
    {
        parent::__construct($receiver, $message);
    }

    /**
     * String message type identifier for mapping on delivery channel
     *
     * @return string
     */
    public function getMessageType()
    {
        return 'test';
    }
}