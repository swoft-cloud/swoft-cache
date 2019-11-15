<?php declare(strict_types=1);

namespace Swoft\Cache;

use Psr\SimpleCache\CacheInterface;
use Swoft\Cache\Adapter\ArrayAdapter;
use Swoft\Cache\Adapter\CoFileAdapter;
use Swoft\Cache\Adapter\FileAdapter;
use Swoft\Cache\Adapter\MemTableAdapter;

/**
 * Class CacheManager
 *
 * @since 2.0.7
 */
class CacheManager implements CacheInterface
{
    public const ADAPTER_FILE   = 'file';
    public const ADAPTER_COFILE = 'cofile';
    public const ADAPTER_ARRAY  = 'array';
    public const ADAPTER_MTABLE = 'mTable';

    /**
     * Current used cache adapter driver
     *
     * @var string
     */
    private $adapter = Cache::ADAPTER;

    /**
     * @var array
     */
    private $adapters = [
        'file'   => FileAdapter::class,
        'coFile' => CoFileAdapter::class,
        'array'  => ArrayAdapter::class,
        'mTable' => MemTableAdapter::class,
    ];

    /**
     * Init cache manager
     */
    public function init(): void
    {

    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        return $this->getAdapter()->set($key, $value, $ttl);
    }

    /**
     * @param string $adapter
     *
     * @return CacheInterface
     */
    public function getAdapterClass(string $adapter = ''): string
    {
        return $this->adapters[$adapter] ?? $this->adapter;
    }

    /**
     * @param string $adapter
     *
     * @return CacheInterface
     * @throws \InvalidArgumentException When driver does not exist
     */
    public function getAdapter(string $adapter = ''): CacheInterface
    {
        $currentDriver = $adapter ?? $this->adapter;
        $adapters       = $this->getAdapters();

        if (!isset($adapters[$currentDriver])) {
            throw new \InvalidArgumentException(sprintf('Driver %s not exist', $currentDriver));
        }

        //TODO If driver component not loaded, throw an exception.

        return \Swoft::getBean($adapters[$currentDriver]);
    }

    /**
     * @return array
     */
    public function getAdapters(): array
    {
        return $this->adapters;
    }

    /**
     * @param array $adapters
     */
    public function setAdapters(array $adapters): void
    {
        $this->adapters = array_merge($this->adapters, $adapters);
    }

    /**
     * @param string $adapter
     */
    public function setAdapter(string $adapter): void
    {
        $this->adapter = $adapter;
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        // TODO: Implement get() method.
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null): array
    {
        // TODO: Implement getMultiple() method.
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null)
    {
        // TODO: Implement setMultiple() method.
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple($keys)
    {
        // TODO: Implement deleteMultiple() method.
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        // TODO: Implement has() method.
    }
}
