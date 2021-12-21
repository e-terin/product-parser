<?php

namespace App\Collection;

use Ds\Map;
use InvalidArgumentException;

abstract class MapCollection extends AbstractCollection
{
    public function __construct()
    {
        $this->collection = new Map();
    }

    public function put($key, $value): void
    {
        if($this->checkType($value)){
            $this->collection->put($key, $value);
        }
        else{
            throw new InvalidArgumentException('Attempt to add wrong type object. Waiting type: ' . static::TYPE);
        }

    }

}