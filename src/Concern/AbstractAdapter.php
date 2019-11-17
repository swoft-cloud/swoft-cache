<?php declare(strict_types=1);

namespace Swoft\Cache\Concern;

use Swoft\Cache\Contract\CacheAdapterInterface;
use Swoft\Cache\Exception\InvalidArgumentException;
use Swoft\Contract\EncrypterInterface;
use Swoft\Serialize\Contract\SerializerInterface;
use function is_array;
use function is_string;

/**
 * Class AbstractAdapter
 *
 * @since 2.0.7
 */
abstract class AbstractAdapter implements CacheAdapterInterface
{
    /**
     * The prefix for session key
     *
     * @var string
     */
    protected $prefix = 'cache_';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    private $encrypt = false;

    /**
     * TODO The encrypter instance. for encrypt data
     *
     * @var EncrypterInterface
     */
    protected $encrypter;

    /**
     * Data serializer
     *
     * @var null|SerializerInterface
     */
    protected $serializer;

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return true;
    }

    /**
     * @param $key
     */
    protected function checkKey($key): void
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('The cache key must be an string');
        }
    }

    /**
     * @param $keys
     */
    protected function checkKeys($keys): void
    {
        if (!is_array($keys)) {
            throw new InvalidArgumentException('The cache keys must be an string array');
        }
    }

    protected function serialize($data): string
    {
        if ($this->serializer) {
            return $this->serializer->serialize($data);
        }

        return (string)$data;
    }

    protected function unserialize(string $string)
    {
        if ($this->serializer) {
            return $this->serializer->unserialize($string);
        }

        return $string;
    }

    /**
     * @return bool
     */
    public function isEncrypt(): bool
    {
        return $this->encrypt;
    }

    /**
     * @param bool $encrypt
     */
    public function setEncrypt(bool $encrypt): void
    {
        $this->encrypt = $encrypt;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        return $this->prefix . $key;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return SerializerInterface|null
     */
    public function getSerializer(): ?SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }
}
