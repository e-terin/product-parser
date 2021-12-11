<?php

namespace App\Collection;

use App\Domain\Product;

/**
 * @method Product get(mixed $product_id)
 */
class ProductCollection extends MapCollection
{
    protected const TYPE = Product::class;
}
