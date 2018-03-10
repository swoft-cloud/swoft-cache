<?php

namespace Swoft\Cache;

use Swoft\Core\AbstractDataResult;

/**
 * The result of data
 */
class CacheDataResult extends AbstractDataResult
{
    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function getResult(...$params)
    {
        if($this->pool !== null && $this->connection !== null){
            $this->pool->release($this->connection);
        }
        return $this->data;
    }
}