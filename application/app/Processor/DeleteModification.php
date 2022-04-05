<?php

namespace App\Processor;

use App\Domain\Product;

/**
 * Удаляет массив модификаций
 */
class DeleteModification extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $modifications_to_delete = $this->settings['modifications'];

        $this->output->map(function($id, Product $product) use ($modifications_to_delete) {
            $product->deleteModification($modifications_to_delete);
            return true;
        });
    }
}