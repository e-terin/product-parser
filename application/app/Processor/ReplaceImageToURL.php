<?php

namespace App\Processor;

/**
 * Заменяет указанную картинку на определенный URL в элементах коллекции Product
 */
class ReplaceImageToURL extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $settings = $this->settings;


        $this->output->map(function($id, $product) use ($settings) {

        // replace image
        foreach ($settings as $image_number => $image_value) {
            $product->replaceImage($image_number, $image_value);
        }

            return true;
        });
    }
}