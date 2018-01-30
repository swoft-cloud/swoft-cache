<?php

namespace Swoft\Cache\Bootstrap;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Cache\Cache;
use Swoft\Core\BootBeanIntereface;

/**
 * The core bean of cache
 *
 * @BootBean()
 */
class CoreBean implements BootBeanIntereface
{
    /**
     * @return array
     */
    public function beans()
    {
        return [
            'cache' => [
                'class' => Cache::class,
            ]
        ];
    }
}