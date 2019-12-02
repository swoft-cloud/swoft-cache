<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use RuntimeException;
use Swoft\Cache\Concern\FileSystemTrait;

/**
 * Class FileAdapter
 *
 * @since 2.0.8
 */
class FileAdapter extends ArrayAdapter
{
    use FileSystemTrait;

    /**
     * @var string
     */
    protected $dataFile = '';

    public function init(): void
    {
        if (!$this->dataFile) {
            throw new RuntimeException('must set an datafile for storage cache data');
        }

        $this->loadData();
    }

    public function loadData(): void
    {
        $file = $this->dataFile;

        if ($string = $this->doRead($file)) {
            $this->setData($this->getSerializer()->unserialize($string));
        }
    }

    /**
     * @return bool
     */
    public function saveData(): bool
    {
        $file   = $this->dataFile;
        $string = '';

        if ($data = $this->getData()) {
            $string = $this->getSerializer()->serialize($data);
        }

        return $this->doWrite($file, $string);
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
        if (parent::set($key, $value, $ttl)) {
            return $this->saveData();
        }

        return false;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function delete($key): bool
    {
        if (parent::delete($key)) {
            $this->saveData();
        }

        return false;
    }

    /**
     * @param array        $values
     * @param null|integer $ttl
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        if (parent::setMultiple($values, $ttl)) {
            $this->saveData();
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
        if (parent::deleteMultiple($keys)) {
            $this->saveData();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        parent::clear();

        return $this->saveData();
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        return $this->saveData();
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
