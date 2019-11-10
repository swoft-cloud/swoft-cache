<?php declare(strict_types=1);

namespace Swoft\Cache\Handler;

use Psr\SimpleCache\InvalidArgumentException;
use Swoft\Cache\Concern\AbstractHandler;
use Swoft\Redis\Pool;
use function class_exists;
use function count;

/**
 * Class RedisHandler
 *
 * @since 2.0.7
 */
class RedisHandler extends AbstractHandler
{
    /**
     * @var Pool
     */
    private $redis;

    /**
     * The prefix for session key
     *
     * @var string
     */
    protected $prefix = 'swoft_cache:';

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return class_exists(Pool::class);
    }

    /**
     * {@inheritDoc}
     */
    public function read(string $key): string
    {
        $cacheKey = $this->getCacheKey($key);

        return (string)$this->redis->get($cacheKey);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $key, string $value): bool
    {
        $cacheKey = $this->getCacheKey($key);

        return (bool)$this->redis->set($cacheKey, $value, $this->expireTime);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $key): bool
    {
        return (int)$this->redis->del($key) === 1;
    }

    /**
     * {@inheritDoc}
     */
    public function open(string $savePath, string $name): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc(int $maxLifetime): bool
    {
        return true;
    }

    /**
     * @param Pool $redis
     */
    public function setRedis(Pool $redis): void
    {
        $this->redis = $redis;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return $this->redis->exists($key);
    }

    /**
     * @param string       $key
     * @param mixed        $value
     * @param null|integer $ttl
     *
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
    {
        $cacheKey = $this->getCacheKey($key);

        return (bool)$this->redis->set($cacheKey, $value, (int)$ttl);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key): bool
    {
        return $this->redis->del($key) === 1;
    }

    /**
     * @param iterable|array $values
     * @param null|integer   $ttl
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        return $this->redis->mset($values, $ttl);
    }

    /**
     * @param array $keys
     *
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        $this->checkKeys($keys);

        return $this->redis->del(...$keys) === count($keys);
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        $this->checkKey($key);

        $value = $this->redis->get($key);

        return $value === false ? $default : $value;
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(): bool
    {
        return true;
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param array $keys    A list of keys that can obtained in a single operation.
     * @param mixed $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        return $this->redis->mget($keys);
    }
}
