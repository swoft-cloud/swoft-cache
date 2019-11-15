<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Swoft\Cache\Concern\AbstractAdapter;
use Swoft\Redis\Pool;
use function class_exists;
use function count;

/**
 * Class RedisAdapter
 *
 * @since 2.0.7
 */
class RedisAdapter extends AbstractAdapter
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
        return $this->redis->mset($values, (int)$ttl);
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        return $this->redis->mget((array)$keys);
    }
}