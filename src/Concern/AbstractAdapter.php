<?php declare(strict_types=1);

namespace Swoft\Cache\Concern;

use DateInterval;
use DateTime;
use Swoft\Cache\Contract\CacheAdapterInterface;
use Swoft\Cache\Exception\InvalidArgumentException;
use Swoft\Contract\EncrypterInterface;
use Swoft\Serialize\Contract\SerializerInterface;
use Swoft\Serialize\PhpSerializer;
use Traversable;
use function get_class;
use function gettype;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function iterator_to_array;
use function sprintf;

/**
 * Class AbstractAdapter
 *
 * @since 2.0.7
 */
abstract class AbstractAdapter implements CacheAdapterInterface
{
    public const TIME_KEY = 't';
    public const DATA_KEY = 'd';

    /**
     * The prefix for cache key
     *
     * @var string
     */
    protected $prefix = 'cache_';

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
     * @var SerializerInterface
     */
    private $serializer;

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
     * @param array|Traversable|mixed $keys
     *
     * @return array
     */
    protected function checkKeys($keys): array
    {
        if ($keys instanceof Traversable) {
            $keys = iterator_to_array($keys, false);
        } elseif (!is_array($keys)) {
            throw new InvalidArgumentException('The cache keys must be an string array');
        }

        return $keys;
    }

    /**
     * @param int|DateInterval|mixed $ttl
     *
     * @return int
     */
    protected function formatTTL($ttl): int
    {
        if (is_int($ttl)) {
            return $ttl < 1 ? 0: $ttl;
        }

        if ($ttl instanceof DateInterval) {
            $ttl = (int)DateTime::createFromFormat('U', 0)->add($ttl)->format('U');
        }

        $msgTpl = 'Expiration date must be an integer, a DateInterval or null, "%s" given';
        throw new InvalidArgumentException(sprintf($msgTpl, is_object($ttl) ? get_class($ttl) : gettype($ttl)));
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
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        if (!$this->serializer) {
            $this->serializer = new PhpSerializer();
        }

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
