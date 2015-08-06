<?php

namespace Feedbee\SmartNotificationService\Test\Emitter;

class Custom implements EmitterInterface
{
    const EVENT_ACTIVATED = 'activated';

    private $listeners = [];

    public function activate($value) {
        $this->triggerEvent(self::EVENT_ACTIVATED, $this, ['value' => $value]);
    }

    public function attachListener($eventName, $listener) {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][] = $listener;
    }

    private function triggerEvent($eventName, $target, array $params = array()) {
        $event = new \StdClass;
        $event->name = $eventName;
        $event->target = $target;
        $event->params = $params;

        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $listener($event);
            }
        }
    }
}