<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

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
    /**
     * @var MemTable
     */
    private $table;

    /**
     * @var int
     */
    private $size = 10240;

    /**
     * @var string
     */
    private $name = 'mem-cache-table';

    /**
     * @var array
     */
    private $columns = [
        self::TIME_KEY => [Table::TYPE_INT, 10],
        self::DATA_KEY => [Table::TYPE_STRING, 10240],
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
     * Init instance.
     * Will called on swoft bean created.
     */
    public function init(): void
    {
        if (!$this->table) {
            $this->table = new MemTable($this->name, $this->size, $this->columns);
        }

        // Create swoole table.
        // You should create it before swoole server start.
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
        $this->checkKey($key);
        $ttl = $this->formatTTL($ttl);

        return $this->table->set($key, [
            self::TIME_KEY => $ttl > 0 ? time() + $ttl : 0,
            self::DATA_KEY => $value,
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
        $ttl = $this->formatTTL($ttl);

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
        $keys = $this->checkKeys($keys);

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
        if ($row[self::TIME_KEY] < time()) {
            $this->table->del($key);
            return $default;
        }

        return $row[self::DATA_KEY];
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $rows = [];
        $keys = $this->checkKeys($keys);

        foreach ($keys as $key) {
            $rows[$key] = $this->get($key, $default);
        }

        return $rows;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }
}
