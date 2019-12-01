<?php declare(strict_types=1);

namespace Swoft\Cache;

use Psr\SimpleCache\InvalidArgumentException;
use Swoft;

/**
 * Class Cache
 *
 * @since 2.0.07
 */
final class Cache
{
    // Cache manager bean name
    public const MANAGER    = 'cacheManager';
    public const ADAPTER    = 'cacheAdapter';
    public const SERIALIZER = 'cacheSerializer';

    /**
     * @return CacheManager
     */
    public static function manager(): CacheManager
    {
        return Swoft::getBean(self::MANAGER);
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function get(string $key, $default = null)
    {
        return self::manager()->get($key, $default);
    }

    /**
     * @param string $key
     * @param        $value
     * @param int    $ttl
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function set(string $key, $value, int $ttl = 0): bool
    {
        return self::manager()->set($key, $value, $ttl);
    }

    /**
     * @param string $key
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function delete(string $key): bool
    {
        return self::manager()->delete($key);
    }
}
