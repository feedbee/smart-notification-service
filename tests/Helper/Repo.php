<?php

namespace Feedbee\SmartNotificationService\Tests\Helper;

class Repo
{
    private $map;

    public function get($name)
    {
        return $this->map[$name];
    }

    public function set($name, $value)
    {
        $this->map[$name] = $value;
    }
}