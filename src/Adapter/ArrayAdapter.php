<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Swoft\Cache\Concern\AbstractAdapter;
use Swoft\Cache\Exception\InvalidArgumentException;
use function time;

/**
 * Class ArrayAdapter
 *
 * @since 2.0.7
 */
class ArrayAdapter extends AbstractAdapter
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $this->checkKey($key);

        if (!isset($this->data[$key])) {
            return $default;
        }

        $row = $this->data[$key];

        // Check expire time
        $expireTime = $row[self::TIME_KEY];
        if ($expireTime > 0 && $expireTime < time()) {
            unset($this->data[$key]);
            return $default;
        }

        return $row[self::DATA_KEY];
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value, $ttl = null): bool
    {
        $this->checkKey($key);

        $ttl = $this->formatTTL($ttl);

        $this->data[$key] = [
            self::TIME_KEY => $ttl > 0 ? time() + $ttl : 0,
            self::DATA_KEY => $value,
        ];

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key): bool
    {
        $this->checkKey($key);

        if (isset($this->data[$key])) {
            unset($this->data[$key]);
            return true;
        }

        return false;
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(): bool
    {
        $this->data = [];
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $keys = $this->checkKeys($keys);

        $values = [];
        foreach ($keys as $key) {
            $values[] = $this->get($key, $default);
        }

        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function setMultiple($values, $ttl = null): bool
    {
        if (!is_array($values)) {
            throw new InvalidArgumentException('The cache keys must be an string array');
        }

        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * {@inheritDoc}
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
    public function has($key): bool
    {
        $this->checkKey($key);

        return isset($this->data[$key]);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
