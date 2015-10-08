<?php

namespace Feedbee\SmartNotificationService\EventHandler;

use Feedbee\SmartNotificationService\Service\DeliveryService;
use Feedbee\SmartNotificationService\Notification\NotificationInterface;

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

    /**
     * @param NotificationInterface[] $notifications
     */
    protected function sendNotifications(array $notifications)
    {
        foreach ($notifications as $notification) {
            $this->sendNotification($notification);
        }
    }

    /**
     * @param NotificationInterface $notification
     */
    protected function sendNotification(NotificationInterface $notification)
    {
        $messages = $notification->getMessages();
        foreach ($messages as $message) {
            $this->getDeliveryService()->sendMessage($message);
        }
    }
}