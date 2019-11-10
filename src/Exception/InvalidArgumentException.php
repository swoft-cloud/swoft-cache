<?php declare(strict_types=1);

namespace Swoft\Cache\Exception;

use RuntimeException;

/**
 * Class CacheException
 *
 * @since 2.0.7
 */
class InvalidArgumentException extends RuntimeException implements \Psr\SimpleCache\InvalidArgumentException
{

}
