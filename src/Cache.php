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
     * @return DriverInterface
     */
    public function getCache(string $driver = null): DriverInterface
    {
        $cacheDriver = $this->driver;
        $drivers = $this->mergeDrivers();

        if ($driver !== null) {
            $cacheDriver = $driver;
        }

        if (! isset($drivers[$cacheDriver])) {
            throw new \InvalidArgumentException(sprintf('Cache driver %s not exist', $cacheDriver));
        }

        //TODO If driver component not loaded, throw an exception.

        return App::getBean($drivers[$cacheDriver]);
    }

    /**
     * @param string $method
     * @param array  $arguments
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call($method, $arguments)
    {
        $cache = $this->getCache();

        return $cache->$method(...$arguments);
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
