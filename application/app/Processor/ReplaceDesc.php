<?php

namespace App\Processor;

/**
 * Заменяет Описание
 */
class ReplaceDesc extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $new_desc = $this->settings['new_desc'];

        $this->output->map(function($id, $product) use ($new_desc) {

            $new_desc_replace =  !empty($product->attributes['brand_car'])
                ? str_replace(
                    '{brand}',
                    $product->attributes['brand_car'],
                    $new_desc
                    )
                : $new_desc;
                $product->desc = $new_desc_replace;
            return true;
        });
    }
}