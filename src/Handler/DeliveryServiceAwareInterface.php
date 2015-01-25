<?php

namespace Feedbee\SmartNotificationService\handler;

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