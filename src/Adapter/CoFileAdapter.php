<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Swoft\Co;
use function extension_loaded;

/**
 * Class CoFileAdapter
 *
 * @since 2.0.7
 */
class CoFileAdapter extends FileAdapter
{
    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return extension_loaded('swoole');
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function read(string $file): string
    {
        return Co::readFile($file);
    }

    /**
     * @param string $file
     * @param string $string
     *
     * @return bool
     */
    public function doWrite(string $file, string $string): bool
    {
        return Co::writeFile($file, $string) !== false;
    }
}
