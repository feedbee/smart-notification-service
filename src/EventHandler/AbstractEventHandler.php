<?php

namespace Feedbee\SmartNotificationService\EventHandler;

use Feedbee\SmartNotificationService\Service\DeliveryService;

abstract class AbstractEventHandler implements EventHandlerInterface
{
    /**
     * @var DeliveryService
     */
    private $deliveryService;

    /**
     * @param DeliveryService $deliveryService
     */
    public function setDeliveryService(DeliveryService $deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    /**
     * @return DeliveryService
     */
    public function getDeliveryService()
    {
        return $this->deliveryService;
    }
}