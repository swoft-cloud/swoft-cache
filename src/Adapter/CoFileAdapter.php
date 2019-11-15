<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use RuntimeException;
use Swoft\Co;
use function extension_loaded;

/**
 * Class CoFileAdapter
 *
 * @since 2.0.7
 */
class CoFileAdapter extends FileAdapter
{
    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return extension_loaded('swoole');
    }

    /**
     * @param string $id
     *
     * @return string
     * @throws RuntimeException
     */
    public function read(string $id): string
    {
        $file = $this->getCacheFile($id);
        if (!file_exists($file)) {
            return '';
        }

        // If data has been expired
        if (filemtime($file) + $this->expireTime < time()) {
            unlink($file);
            return '';
        }

        return Co::readFile($file);
    }

    /**
     * @param string $id
     * @param string $data
     *
     * @return bool
     */
    public function write(string $id, string $data): bool
    {
        return Co::writeFile($this->getCacheFile($id), $data) !== false;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        // TODO: Implement has() method.
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
        // TODO: Implement set() method.
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key): bool
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param array        $values
     * @param null|integer $ttl
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        // TODO: Implement setMultiple() method.
    }

    /**
     * @param array $keys
     *
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        // TODO: Implement deleteMultiple() method.
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        // TODO: Implement clear() method.
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
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        // TODO: Implement getMultiple() method.
    }
}
