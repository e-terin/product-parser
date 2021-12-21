<?php

namespace App\Parser;

use App\Collection\ProductCollection;

abstract class AbstractProductConverter
{
    protected ProductCollection $input;
    protected ProductCollection $output;

    public function __construct(ProductCollection $products)
    {
        $this->input = $products;
    }

    public function getResult()
    {
        return $this->output;
    }

    abstract public function convert();
}