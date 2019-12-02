<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Swoft\Cache\Concern\AbstractAdapter;
use Swoft\Cache\Concern\FileSystemTrait;
use Swoft\Stdlib\Helper\Dir;
use Swoft\Stdlib\Helper\Sys;
use function file_exists;
use function filemtime;
use function glob;
use function is_dir;
use function md5;
use function time;
use function unlink;

/**
 * Class MultiFileAdapter
 */
class MultiFileAdapter extends AbstractAdapter
{
    use FileSystemTrait;

    /**
     * @var string
     */
    private $savePath = '';

    /**
     * Init $savePath directory
     */
    public function init(): void
    {
        if (!$this->savePath) {
            $this->savePath = Sys::getTempDir() . '/swoft-caches';
        }

        if (!is_dir($this->savePath)) {
            Dir::make($this->savePath);
        }
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
        $file = $this->getCacheFile($key);
        $ttl  = $this->formatTTL($ttl);

        $string = $this->getSerializer()->serialize([
            self::TIME_KEY => $ttl > 0 ? time() + $ttl : 0,
            self::DATA_KEY => $value,
        ]);

        return $this->doWrite($file, $string);
    }

    /**
     * @param array        $values
     * @param null|integer $ttl
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $ttl = $this->formatTTL($ttl);

        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key): bool
    {
        $file = $this->getCacheFile($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    /**
     * @param array $keys
     *
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        $keys = $this->checkKeys($keys);

        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $this->checkKey($key);

        $file = $this->getCacheFile($key);
        if (!$string = $this->doRead($file)) {
            return $default;
        }

        $item = $this->getSerializer()->unserialize($string);

        // Check expire time
        $expireTime = $item[self::TIME_KEY];
        if ($expireTime > 0 && $expireTime < time()) {
            $this->doDelete($file);
            return $default;
        }

        return $item[self::DATA_KEY];
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $keys = $this->checkKeys($keys);

        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }

        return $values;
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
     * @param string $key
     *
     * @return string
     */
    protected function getCacheFile(string $key): string
    {
        return $this->savePath . '/' . $this->prefix . md5($key);
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
}
