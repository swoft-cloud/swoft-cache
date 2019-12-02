<?php declare(strict_types=1);

namespace Swoft\Cache\Concern;

use Swoft\Stdlib\Helper\Dir;
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function unlink;

/**
 * Trait FileSystemTrait
 *
 * @since 2.0.8
 */
trait FileSystemTrait
{
    /**
     * @param string $file
     *
     * @return string
     */
    protected function doRead(string $file): string
    {
        if (!file_exists($file)) {
            return '';
        }

        return (string)file_get_contents($file);
    }

    /**
     * @param string $file
     * @param string $data
     *
     * @return bool
     */
    protected function doWrite(string $file, string $data): bool
    {
        $cacheDir = dirname($file);
        if (!is_dir($cacheDir)) {
            Dir::make($cacheDir);
        }

        return file_put_contents($file, $data) !== false;
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    protected function doDelete(string $file): bool
    {
        return unlink($file);
    }

}
