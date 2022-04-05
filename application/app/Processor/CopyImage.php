<?php

namespace App\Processor;

/**
 * Копирует номер картинки, пришедшей в параметре number в конец массива картинок
 */
class CopyImage extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $number = $this->settings['number'];
        if ($number === null){
            throw new \RuntimeException('Can`t find required param "number" in process CopyImage');
        }

        $this->output->map(function($id, $product) use ($number) {
            if($product->copyImage($number)){
                return true;
            }
            throw new \RuntimeException('Error in $product->copyImage('.$number.') in product ID# ' . $product->id);
        });
    }
}