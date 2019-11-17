<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Swoft\Cache\Concern\AbstractAdapter;
use Swoft\Stdlib\Helper\Dir;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function glob;
use function is_dir;
use function time;
use function unlink;

/**
 * Class FileAdapter
 */
class FileAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    private $savePath = '/tmp/swoft-caches';

    /**
     * Init $savePath directory
     */
    public function init(): void
    {
        if (empty($this->options['savePath'])) {
            $this->options['savePath'] = '/tmp/swoft-caches';
        }

        $savePath = $this->options['savePath'];

        if (!is_dir($this->savePath)) {
            Dir::make($this->savePath);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    /**
     * @param string $id
     *
     * @return string
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

        return (string)file_get_contents($file);
    }

    /**
     * @param string $id
     * @param string $data
     *
     * @return bool
     */
    public function write(string $id, string $data): bool
    {
        return file_put_contents($this->getCacheFile($id), $data) !== false;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function destroy(string $id): bool
    {
        $file = $this->getCacheFile($id);
        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    /**
     * @param int $maxLifetime
     *
     * @return bool
     */
    public function gc(int $maxLifetime): bool
    {
        $curTime = time();

        foreach (glob("{$this->savePath}/{$this->prefix}*") as $file) {
            if (file_exists($file) && (filemtime($file) + $maxLifetime) < $curTime) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Close the session, will clear all session data.
     *
     * @return bool
     */
    public function close(): bool
    {
        // return $this->gc(-1);
        return true;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getCacheFile(string $key): string
    {
        return $this->savePath . '/' . $this->prefix . $key;
    }

    /**
     * @return string
     */
    public function getSavePath(): string
    {
        return $this->savePath;
    }

    /**
     * @param string $savePath
     */
    public function setSavePath(string $savePath): void
    {
        $this->savePath = $savePath;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return file_exists($this->getCacheFile($key));
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
        foreach (glob("{$this->savePath}/{$this->prefix}*") as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $this->checkKey($key);

        $text = file_get_contents($this->getCacheFile($key));

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
