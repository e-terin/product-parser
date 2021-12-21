<?php

namespace App\Processor;

/**
 * Проверяет кол-во картинок в коллекции Product
 */
class ReplaceImage extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $settings = $this->settings;


        $this->output->map(function($id, $product) use ($settings) {

            // change template to real product id in
            $new_images = array_map( function ($image_template) use ($product) {
                $a = str_replace('{id}', $product->id, $image_template);
                return ($a);
            },  $settings);

            // replace image
            foreach ($new_images as $image_number => $image_value) {
                $product->replaceImage($image_number, $image_value);
            }

            return true;
        });
    }
}