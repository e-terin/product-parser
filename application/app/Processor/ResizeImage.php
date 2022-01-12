<?php

namespace App\Processor;

use Imagick;
use ImagickPixel;

/**
 * Проверяет кол-во картинок в коллекции Product
 */
class ResizeImage extends AbstractProcessor
{
    private const OUTPUT_FORMAT = 'jpg';

    /**
     * @throws \ImagickException
     */
    public function process()
    {
        $products = $this->params->getCollection();

        // TODO: check directory

        foreach ($products as $product_id=>$product){
            foreach ($this->settings['images'] as $image){
                if (!empty($product->images[$image])) {
                    $img = new Imagick($product->images[$image]);
                    $width = $img->getImageWidth();
                    $height = $img->getImageHeight();
                    $new_height = $this->settings['output']['height'];
                    $new_width = $new_height  * $width / $height;
                    $img->resizeImage($new_width, $new_height, Imagick::FILTER_UNDEFINED, 1, 1, 1);
                    $img->setImageFormat($this->settings['output']['format']);

                    $output_image_dir = $this->scenario_settings['dir_out']
                        . $this->scenario_settings['category']
                        . '/'
                        . $image
                        . '/';

                    if (!is_dir($output_image_dir) && !mkdir($output_image_dir) && !is_dir($output_image_dir)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $output_image_dir));
                    }

                    $output_image_dir .= !empty($product->attributes['brand_car'])
                        ?   $product->attributes['brand_car'] . '/'
                        : '';

                    if (!is_dir($output_image_dir) && !mkdir($output_image_dir) && !is_dir($output_image_dir)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $output_image_dir));
                    }

                    $output_image_name = $output_image_dir
                         . $product_id
                         . '.'
                         . self::OUTPUT_FORMAT;

                    $img->writeImage($output_image_name);

                    if ($this->scenario_settings['test'] ?? false) {
                        break;
                    }
                }
            }

        }

        $this->output = $this->params;
    }
}