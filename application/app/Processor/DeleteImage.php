<?php

namespace App\Processor;

use App\Domain\Product;

/**
 * Удаляет заданную картинку кол-во картинок в коллекции Product
 */
class DeleteImage extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $settings = $this->settings;

        $this->output->map(function($id, Product $product) use ($settings) {

            foreach ($settings['images'] as $image_number) {
                $product->deleteImage($image_number);
            }

            return true;
        });
    }
}