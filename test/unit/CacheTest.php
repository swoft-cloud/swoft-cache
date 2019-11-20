<?php

namespace SwoftTest\Cache;

use PHPUnit\Framework\TestCase;
use Swoft\Cache\Adapter\ArrayAdapter;
use Swoft\Cache\CacheManager;
use Swoft\Cache\Exception\InvalidArgumentException;
use SwoftTest\Testing\Concern\CommonTestAssertTrait;

/**
 * Class CacheTest
 */
class CacheTest extends TestCase
{
    use CommonTestAssertTrait;

    /**
     * @throws InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testCache(): void
    {
        $cache = new CacheManager();
        $cache->setAdapter(new ArrayAdapter());

        $key   = 'test:key';
        $tests = [
            'string',
            1,
            1.235,
            false,
            ['int' => 1, 'float' => 1.234, 'bool' => true, 'string' => 'value'],
        ];

        foreach ($tests as $value) {
            $ok = $cache->set($key, $value);

            $this->assertTrue($ok);
            $this->assertTrue($cache->has($key));

            $this->assertEquals($value, $cache->get($key));

            $this->assertTrue($condition);
        }

        foreach ([12, true, null, ''] as $key) {
            $this->assetException(function () use ($cache, $key) {
                $cache->set($key, 'value');
            }, InvalidArgumentException::class);

            $this->assetException(function () use ($cache, $key) {
                $cache->get($key);
            }, InvalidArgumentException::class);
        }

        /**
         * Delete
         */
        $deleteResult = $cache->delete($key);
        $this->assertTrue($deleteResult);
        $getResultAfterDelete = $cache->get($key);
        $this->assertNull($getResultAfterDelete);

        /**
         * clear
         */
        $cache->set($key, $stringValue);
        $clearResult = $cache->clear();
        $this->assertTrue($clearResult);
        $getResultAfterClear = $cache->get($key);
        $this->assertNull($getResultAfterClear);

        /**
         * Has
         */
        $cache->set($key, $stringValue);
        // when exist
        $hasResult = $cache->has($key);
        $this->assertTrue($hasResult);
        // When not exist
        $cache->delete($key);
        $hasResult = $cache->has($key);
        $this->assertFalse($hasResult);

        /**
         * setMultiple & getMultiple
         */
        $multiple     = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        $setMulResult = $cache->setMultiple($multiple);
        $this->assertTrue($setMulResult);
        $getMulResult = $cache->getMultiple(['key1', 'key2']);
        $this->assertEquals($multiple, $getMulResult);
    }
}
