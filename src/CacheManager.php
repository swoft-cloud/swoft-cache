<?php declare(strict_types=1);

namespace Swoft\Cache;

use Psr\SimpleCache\CacheInterface;
use Swoft\Redis\Pool;
use Swoft\Serialize\Contract\SerializerInterface;

/**
 * @method string|bool get($key, $default = null)
 * @method bool delete($key)
 * @method bool clear()
 * @method array getMultiple($keys, $default = null)
 * @method bool setMultiple($values, $ttl = null)
 * @method bool deleteMultiple($keys)
 * @method int has($key)
 */
class CacheManager // implements CacheInterface
{
    /**
     * Current used cache adapter driver
     *
     * @var string
     */
    private $adapter = 'redis';

    /**
     * @var array
     */
    private $adapters = [];

    /**
     * Init cache manager
     */
    public function init(): void
    {

    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param int|double|string|bool $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and the driver
     *                                      supports TTL then the library may set a default value for it or let the
     *                                      driver take care of that.
     *
     * @return bool True on success and false on failure.
     * @throws \InvalidArgumentException If the $value string is not a legal value
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        return $this->getAdapter()->set($key, $value, $ttl);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     * @throws \RuntimeException If the $method does not exist
     * @throws \InvalidArgumentException If the driver dose not exist
     */
    public function __call($method, $arguments)
    {
        $availableMethods = [
            'has',
            'get',
            'set',
            'delete',
            'getMultiple',
            'setMultiple',
            'deleteMultiple',
            'clear',
        ];
        if (!\in_array($method, $availableMethods, true)) {
            throw new \RuntimeException(sprintf('Method not exist, method=%s', $method));
        }
        $driver = $this->getAdapter();
        return $driver->$method(...$arguments);
    }

    /**
     * @param string|null $driver
     *
     * @return CacheInterface
     * @throws \InvalidArgumentException When driver does not exist
     */
    public function getAdapter(string $driver = null): CacheInterface
    {
        $currentDriver = $driver ?? $this->adapter;
        $drivers       = $this->getAdapters();
        if (!isset($drivers[$currentDriver])) {
            throw new \InvalidArgumentException(sprintf('Driver %s not exist', $currentDriver));
        }

        //TODO If driver component not loaded, throw an exception.

        return \Swoft::getBean($drivers[$currentDriver]);
    }

    /**
     * @return array
     */
    public function getAdapters(): array
    {
        return array_merge($this->adapters, $this->defaultDrivers());
    }

    /**
     * @param string $adapter
     */
    public function setAdapter(string $adapter): void
    {
        $this->adapter = $adapter;
    }

    /**
     * Default drivers
     *
     * @return array
     */
    public function defaultDrivers(): array
    {
        return [
            'redis' => Pool::class,
        ];
    }
}
