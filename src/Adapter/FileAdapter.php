<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Swoft\Cache\Concern\AbstractAdapter;

/**
 * Class FileAdapter
 *
 * @since 2.0.8
 */
class FileAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $dataFile;

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
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        // TODO: Implement get() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        // TODO: Implement getMultiple() method.
    }

    /**
     * @return string
     */
    public function getDataFile(): string
    {
        return $this->dataFile;
    }

    /**
     * @param string $dataFile
     */
    public function setDataFile(string $dataFile): void
    {
        $this->dataFile = $dataFile;
    }
}
