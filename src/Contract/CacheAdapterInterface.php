<?php declare(strict_types=1);

namespace Swoft\Cache\Contract;

use Psr\SimpleCache\CacheInterface;

/**
 * Interface CacheAdapterInterface
 *
 * @since 2.0.7
 */
interface CacheAdapterInterface extends CacheInterface
{
    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key): bool;

    /**
     * @param string       $key
     * @param mixed        $value
     * @param null|integer $ttl
     *
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key): bool;

    /**
     * @param array        $values
     * @param null|integer $ttl
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool;

    /**
     * @param array $keys
     *
     * @return bool
     */
    public function deleteMultiple($keys): bool;

    /**
     * @return bool
     */
    public function clear(): bool;
}
