<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Swoft\Co;
use function extension_loaded;
use function file_exists;

/**
 * Class CoMultiFileAdapter
 *
 * @since 2.0.7
 */
class CoMultiFileAdapter extends MultiFileAdapter
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
    public function doRead(string $file): string
    {
        if (!file_exists($file)) {
            return '';
        }

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
