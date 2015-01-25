<?php

namespace Feedbee\SmartNotificationService\DeliveryChannel;

use Feedbee\SmartNotificationService\Message\MessageInterface;

/**
 * Interface for DeliveryChannel class, which sends given message to receiver in custom way.
 */
interface DeliveryChannelInterface
{
    /**
     * Sends given message to receiver in custom way. In case of any error that fails delivery
     * exception must be thrown.
     *
     * @param MessageInterface $message
     * @return void
     */
    public function sendMessage(MessageInterface $message);
}