<?php

namespace App\Collection;

abstract class AbstractCollection
{
    protected $collection;

    protected function checkType($object): bool
    {
        $type = static::TYPE;
        return $object instanceof $type;
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->collection, $name)) {
            return call_user_func_array([$this->collection,$name], $arguments);
        }

        throw new \InvalidArgumentException('Method ' . $name . ' is not exist in class ' . get_class($this->collection));
    }

    public function getCollection()
    {
        return  $this->collection;
    }
}