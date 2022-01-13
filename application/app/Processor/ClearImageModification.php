<?php

namespace App\Processor;

use App\Domain\Product;

/**
 * Удаляет заданную картинку кол-во картинок в коллекции Product
 */
class ClearImageModification extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $settings = $this->settings;

        $this->output->map(function($id, Product $product) {
            if (!empty($product->modifications)) {
                foreach ($product->modifications as $index=>$modification) {
                    $product->clearImageModification($index);
                }
            }

            return true;
        });
    }
}