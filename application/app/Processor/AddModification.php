<?php

namespace App\Processor;

use App\Domain\Product;

/**
 * Принимает в параметре массив модификаций, который имеет определенную структуру
 */
class AddModification extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $modifications = $this->settings['modifications'];

        if (!is_array($modifications)){
            throw new \RuntimeException('Can`t find required param array "modifications" in process AddModification');
        }

        $this->output->map(function ($id, Product $product) use ($modifications) {
            foreach ($modifications as $modification){
                $product->addModification($modification);
            }
            return true;
        });
    }
}