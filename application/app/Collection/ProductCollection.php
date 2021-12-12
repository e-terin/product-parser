<?php

namespace App\Collection;

use App\Domain\Product;

/**
 * @method Product copy()
 * @method Product get(mixed $product_id)
 * @method map(\Closure $param)
 */
class ProductCollection extends MapCollection
{
    protected const TYPE = Product::class;
}
