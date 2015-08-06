<?php

namespace Feedbee\SmartNotificationService\ServiceResolver;

/**
 * Resolve resources creation for needs of Smart Notification Service.
 * Uses factory and/or any type of Service Locator (Repository / IoC container / Service Locator)
 * to get resource instance based on it's name.
 */
class ServiceResolver implements ServiceResolverInterface
{
    private $factory;

    private $locator;

    public function resolve($nameOrInstance)
    {
        // use fabric
        $factory = $this->getFactory();
        if (!is_null($factory)) {
            $result = $factory($nameOrInstance);
            if (!is_null($result)) {
                return $result;
            }
        }

        // lookup in Repository / IoC container / Service Locator
        $locator = $this->getLocator();
        if (!is_null($locator)) {
            $result = $locator->get($nameOrInstance);
            if (!is_null($result)) {
                return $result;
            }
        }

        throw new \RuntimeException("Requested service `$nameOrInstance` not found");
    }

    /**
     * @return \Callable
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param \Callable $factory
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return mixed
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * @param mixed $locator
     */
    public function setLocator($locator)
    {
        $this->locator = $locator;
    }
}