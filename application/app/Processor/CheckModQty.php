<?php

namespace App\Processor;

/**
 * Проверяет кол-во модификаций в коллекции Product
 */
class CheckModQty extends AbstractProcessor
{
    public function process()
    {
        $products = $this->params->getCollection();

        $first = true;
        $prev_qty_mod = 0;

        foreach ($products as $product_id => $product){
            $qty_mod = count($product->modifications);
            if (!$first && $qty_mod!== $prev_qty_mod){
                throw new \RuntimeException('Different quantity images in product ' . $product_id . ' Was ' . $prev_qty_mod . ' now ' . $qty_mod);
            }
            $prev_qty_mod = $qty_mod;
            $first = false;
        }

        $this->output = $this->params;
    }
}