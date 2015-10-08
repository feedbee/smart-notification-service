<?php

namespace Feedbee\SmartNotificationService\EventHandler;

use Feedbee\SmartNotificationService\DeliveryService;

interface DeliveryServiceAwareInterface
{
    /**
     * @param DeliveryService $deliveryService
     */
    public function setDeliveryService(DeliveryService $deliveryService);

    /**
     * @return DeliveryService
     */
    public function getDeliveryService();
}