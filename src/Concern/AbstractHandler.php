<?php declare(strict_types=1);

namespace Swoft\Cache\Concern;

use Swoft\Cache\Exception\InvalidArgumentException;
use Swoft\Contract\EncrypterInterface;
use Swoft\Cache\Contract\CacheHandlerInterface;
use function is_array;
use function is_string;

/**
 * Class AbstractHandler
 *
 * @since 2.0.7
 */
abstract class AbstractHandler implements CacheHandlerInterface
{
    /**
     * The prefix for session key
     *
     * @var string
     */
    protected $prefix = 'cache_';

    /**
     * @var bool
     */
    private $encrypt = false;

    /**
     * TODO The encrypter instance. for encrypt session data
     *
     * @var EncrypterInterface
     */
    protected $encrypter;

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
}
