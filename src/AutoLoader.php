<?php declare(strict_types=1);

namespace Swoft\Cache;

use Swoft\Cache\Adapter\MultiFileAdapter;
use Swoft\Helper\ComposerJSON;
use Swoft\Serialize\PhpSerializer;
use Swoft\SwoftComponent;
use function alias;
use function bean;
use function dirname;

/**
 * Class AutoLoader
 *
 * @since 2.0.7
 */
final class AutoLoader extends SwoftComponent
{
    /**
     * Get namespace and dirs
     *
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * Metadata information for the component.
     *
     * @return array
     * @see ComponentInterface::getMetadata()
     */
    public function metadata(): array
    {
        $jsonFile = dirname(__DIR__) . '/composer.json';

        return ComposerJSON::open($jsonFile)->getMetadata();
    }

    /**
     * @return array
     */
    public function beans(): array
    {
        return [
            Cache::MANAGER    => [
                'class'   => CacheManager::class,
                'adapter' => bean(Cache::ADAPTER),
            ],
            Cache::ADAPTER    => [
                'class'      => MultiFileAdapter::class,
                'serializer' => bean(Cache::SERIALIZER),
                'savePath'   => alias('@runtime/caches'),
                // 'dataFile'   => alias('@runtime/caches/cache.data'),
            ],
            Cache::SERIALIZER => [
                'class' => PhpSerializer::class
            ]
        ];
    }
}
