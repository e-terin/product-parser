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

            if(!isset($product->id) || !isset($product->attributes['brand_car'])){
                //throw new \RuntimeException('Necessary properties of product is absent: product->id or brand_car');
            }

            // change template to real product id in
            $new_images = array_map( static function ($image_template) use ($product) {
                $a = str_replace(
                    ['{id}', '{brand_car}'],
                    [$product->id, str_replace(' ', '%20', $product->attributes['brand_car'])],
                    $image_template
                );
                return ($a);
            },  $settings);

            // replace image
            foreach ($new_images as $image_number => $image_value) {
                $product->replaceImage($image_number, $_ENV['SOURCE_URL'] . $image_value);
            }

            return true;
        });
    }
}