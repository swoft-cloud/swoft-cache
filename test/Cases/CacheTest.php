<?php

namespace SwoftTest\Cache;

use Swoft\Cache\Cache;

class CacheTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function cache()
    {
        $cache = new Cache();
        $redis = $cache->getDriver('redis');
        echo '<pre>';
        var_dump($redis);
        echo '</pre>';
        exit();
    }
}