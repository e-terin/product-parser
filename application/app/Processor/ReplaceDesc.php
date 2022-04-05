<?php

namespace App\Processor;

/**
 * Заменяет URL картинки внутри по шаблону
 */
class ReplaceDesc extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $new_desc = $this->settings['new_desc'];

        $this->output->map(function($id, $product) use ($new_desc) {
                $product->desc = $new_desc;
            return true;
        });
    }
}