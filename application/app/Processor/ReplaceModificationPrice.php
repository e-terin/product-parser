<?php

namespace App\Processor;

/**
 * Заменяет цены модификаций
 */
class ReplaceModificationPrice extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $settings = $this->settings;

        // todo сделать проверку на сущ $_ENV['SOURCE_URL']
        $this->output->map(function ($id, $product) use ($settings) {
            // change template to real product id in
            foreach ($settings as $old_price=>$new_price){
                $product->replaceModificationPrice($old_price,$new_price);
            }

            return true;
        });
    }
}