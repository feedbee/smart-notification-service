<?php

namespace Feedbee\SmartNotificationService\Message;

/**
 * Abstract basic message that aware receiver and message strings
 */
abstract class BasicMessage implements MessageInterface
{
    /**
     * @var string
     */
    private $receiver;

    /**
     * @var string
     */
    private $message;

    /**
     * @param string $receiver
     * @param string $message
     */
    public function __construct($receiver = null, $message = null)
    {
        $this->receiver = $receiver;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param string $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    function __toString()
    {
        return "{$this->getReceiver()} → {$this->getMessage()}";
    }
}