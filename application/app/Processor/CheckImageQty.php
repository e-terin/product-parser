<?php

namespace App\Processor;

/**
 * Проверяет кол-во картинок в коллекции Product
 */
class CheckImageQty extends AbstractProcessor
{
    public function process()
    {
        $products = $this->params->getCollection();

        $first = true;
        $prev_qty_images = 0;

        foreach ($products as $product_id => $product){
            $qty_images = count($product->images);
            if (!$first && $qty_images !== $prev_qty_images){
                throw new \RuntimeException('Different quantity images in product ' . $product_id);
            }
            $prev_qty_images = $qty_images;
            $first = false;
        }

        $this->output = $this->params;
    }
}