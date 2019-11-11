<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Psr\SimpleCache\InvalidArgumentException;
use Swoft\Cache\Concern\AbstractAdapter;
use Swoft\Swlib\MemTable;
use Swoole\Table;
use function class_exists;
use function time;

/**
 * Class MemTableAdapter
 *
 * @since 2.0.7
 */
class MemTableAdapter extends AbstractAdapter
{
    public const TIME_FIELD = 't';
    public const DATA_FIELD = 'd';

    /**
     * @var MemTable
     */
    private $table;

    /**
     * @var array
     */
    private $columns = [
        self::TIME_FIELD => [Table::TYPE_INT, 10],
        self::DATA_FIELD => [Table::TYPE_STRING, 10240],
    ];

    /**
     * @var string
     */
    private $dataFile = '';

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return class_exists(Table::class);
    }

    /**
     * Init instance
     */
    public function init(): void
    {
        $this->table = new MemTable('mem-table-cache', 10240, $this->columns);

        $ok = $this->table->create();
        if ($ok && $this->dataFile) {
            $this->table->setDbFile($this->dataFile);
            $this->table->restore();
        }
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->table->clear();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        if ($this->dataFile) {
            $this->table->dump();
        }

        return true;
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

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return $this->table->exist($key);
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
        if ($this->serializer) {
            $value = $this->serializer->serialize($value);
        }

        return $this->table->set($key, [
            self::TIME_FIELD => $ttl > 0 ? time() + $ttl : 0,
            self::DATA_FIELD => $value,
        ]);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key): bool
    {
        return $this->table->del($key);
    }

    /**
     * @param array        $values [key => value]
     * @param null|integer $ttl
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @param array $keys
     *
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->table->del($key);
        }

        return true;
    }

    /**
     * Fetches a value from the cache.
     *
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        /** @var array|false $row */
        $row = $this->table->get($key);
        if ($row === false) {
            return $default;
        }

        // Data expired
        if ($row[self::TIME_FIELD] < time()) {
            $this->table->del($key);
            return $default;
        }

        $value = $row[self::DATA_FIELD];

        if ($this->serializer) {
            $value = $this->serializer->unserialize($value);
        }

        return $value;
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable|array A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     * @throws InvalidArgumentException
     */
    public function getMultiple($keys, $default = null)
    {
        $rows = [];

        foreach ($keys as $key) {
            $rows[$key] = $this->get($key, $default);
        }

        return $rows;
    }
}
