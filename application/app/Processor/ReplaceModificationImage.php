<?php

namespace App\Processor;

/**
 * Заменяет картинки модификаций
 */
class ReplaceModificationImage extends AbstractProcessor
{
    public function process()
    {
        $this->output = $this->params;
        $settings = $this->settings;

        // todo сделать проверку на сущ $_ENV['SOURCE_URL']
        $this->output->map(function ($id, $product) use ($settings) {
            // change template to real product id in
            $new_images = array_map(function ($image_template) use ($product) {
                $a = $_ENV['SOURCE_URL'] . str_replace('{id}', $product->id, $image_template); // todo: переделать на парсинг всех свойств
                return ($a);
            }, $settings);

            // replace image
            foreach ($new_images as $image_number => $image_value) {
                $product->replaceModificationImage($image_number, $image_value);
            }

            return true;
        });
    }
}