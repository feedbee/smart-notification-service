<?php

namespace Feedbee\SmartNotificationService;

/**
 *
 */
class DeliveryService
{
    /**
     * @var ServiceResolver\ServiceResolverInterface
     */
    private $deliveryChannelResolver;

    /**
     * @var array
     */
    protected $messageTypeToChannelMap;

    public function sendMessage(Message\MessageInterface $message)
    {
        $this->getSubstituteChannel($message)->sendMessage($message);
    }

    /**
     * @param Message\MessageInterface $message
     * @return DeliveryChannel\DeliveryChannelInterface
     */
    protected function getSubstituteChannel(Message\MessageInterface $message)
    {
        $messageType = $message->getMessageType();

        $channel = $this->getChannelForMessageType($messageType);

        return $this->resolveChannel($channel);
    }

    protected function getChannelForMessageType($messageType)
    {
        $map = $this->getMessageTypeToChannelMap();

        if (!isset($map[$messageType])) {
            throw new \RuntimeException("Trying to send message with type `$messageType`, but no associated "
                . 'delivery channels was found');
        }

        return $map[$messageType];
    }

    private function resolveChannel($nameOrInstance)
    {
        if (is_object($nameOrInstance)) {
            return $nameOrInstance;
        }

        try {
            return $this->getDeliveryChannelResolver()->resolve($nameOrInstance);
        }
        catch (\RuntimeException $e)
        {
            throw new \RuntimeException("Delivery channel `$nameOrInstance` not found");
        }
    }

    /**
     * @return ServiceResolver\ServiceResolverInterface
     */
    public function getDeliveryChannelResolver()
    {
        return $this->deliveryChannelResolver;
    }

    /**
     * @param ServiceResolver\ServiceResolverInterface $deliveryChannelResolver
     */
    public function setDeliveryChannelResolver($deliveryChannelResolver)
    {
        $this->deliveryChannelResolver = $deliveryChannelResolver;
    }

    /**
     * @return array
     */
    public function getMessageTypeToChannelMap()
    {
        return $this->messageTypeToChannelMap;
    }

    /**
     * @param array $messageTypeToChannelMap
     */
    public function setMessageTypeToChannelMap(array $messageTypeToChannelMap)
    {
        $this->messageTypeToChannelMap = $messageTypeToChannelMap;
    }
}