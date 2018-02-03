<?php

namespace Swoft\Cache;

use Swoft\App;

/**
 * Cache
 * @method string|bool get($key, $default = null)
 * @method bool set($key, $value, $ttl = null)
 * @method int delete($key)
 * @method bool clear()
 * @method array getMultiple($keys, $default = null)
 * @method bool setMultiple($values, $ttl = null)
 * @method int deleteMultiple($keys)
 * @method int has($key)
 */
class Cache implements CacheInterface
{
    /**
     * @var string
     */
    private $driver = 'redis';

    /**
     * @var array
     */
    private $drivers = [];

    /**
     * get cache by driver
     *
     * @param string|null $driver
     * @throws \InvalidArgumentException
     * @return CacheInterface
     */
    public function getDriver(string $driver = null): CacheInterface
    {
        $currentDriver = $this->driver;
        $drivers = $this->mergeDrivers();

        if ($driver !== null) {
            $currentDriver = $driver;
        }

        if (! isset($drivers[$currentDriver])) {
            throw new \InvalidArgumentException(sprintf('Cache driver %s not exist', $currentDriver));
        }

        //TODO If driver component not loaded, throw an exception.

        return App::getBean($drivers[$currentDriver]);
    }

    /**
     * @param string $method
     * @param array  $arguments
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call($method, $arguments)
    {
        $driver = $this->getDriver();
        return $driver->$method(...$arguments);
    }

    /**
     * merge driver
     *
     * @return array
     */
    private function mergeDrivers(): array
    {
        return array_merge($this->drivers, $this->defaultDrivers());
    }

    /**
     * Defult drivers
     *
     * @return array
     */
    private function defaultDrivers(): array
    {
        return [];
    }
}
