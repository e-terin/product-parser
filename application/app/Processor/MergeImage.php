<?php

namespace App\Processor;

use Imagick;
use ImagickPixel;

/**
 * Проверяет кол-во картинок в коллекции Product
 */
class MergeImage extends AbstractProcessor
{
    private const MIN_QTY_IMAGES_FOR_PROCESSING = 2;
    private const OUTPUT_WIDTH = 750;
    private const OUTPUT_HEIGHT = 750;
    private const IMG_1_REDUCE_RATE = 0.5;
    private const IMG_2_REDUCE_RATE = 0.8;
    private const OUTPUT_FORMAT = 'jpg';

    /**
     * @throws \ImagickException
     */
    public function process()
    {
        $products = $this->params->getCollection();

        // TODO: check directory

        $img1 = new Imagick(
            $_ENV['DIR_WORK']
            . 'in/'
            . $this->scenario_settings['category']
            . '/'
            . $this->settings['img1']['name']
        );

        // обработка первого изображения
        $img1_width = ceil($this->settings['output']['width'] * $this->settings['img1']['reduce']) ;
        $img1_height = ceil($this->settings['output']['height'] * $this->settings['img1']['reduce']);
        $img1->resizeImage($img1_width, $img1_height, Imagick::FILTER_UNDEFINED, 1, 1, 1);

        foreach ($products as $product_id => $product){
            /* Создание объекта Imagick с поддержкой прозрачности */
            $img = new Imagick();
            $img->newImage(
                $this->settings['output']['width'],
                $this->settings['output']['height'],
                new ImagickPixel('white')
            );
            $img->setImageFormat($this->settings['output']['format']);

            // обработка второго изображения
            $img2 = new Imagick($product->images[$this->settings['img2']['number']]);
            $img2_width = ceil($this->settings['output']['width'] * $this->settings['img2']['reduce']);
            $img2_height = ceil($this->settings['output']['height'] * $this->settings['img2']['reduce']);
            $img2->resizeImage($img2_width, $img2_height, Imagick::FILTER_UNDEFINED, 1, 1, 1);
            if ($this->settings['img2']['rotate'] > 0) {
                $img2->rotateImage('white', $this->settings['img2']['rotate']);
            }

            // накладываем оба изображения на холст
            $img->compositeImage(
                $img1,
                Imagick::COMPOSITE_OVER,
                $this->settings['img1']['offsetX'],
                $this->settings['img1']['offsetY']
            );
            $img->compositeImage(
                $img2,
                Imagick::COMPOSITE_OVER,
                $this->settings['img2']['offsetX'],
                $this->settings['img2']['offsetY']
            );

            // todo check directory
            $output_image_dir =
                $_ENV['DIR_WORK']
                . 'out/'
                . $this->scenario_settings['category']
                . '/'
                . $this->settings['img1']['name']
                . '/';

            if (!is_dir($output_image_dir) && !mkdir($output_image_dir) && !is_dir($output_image_dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $output_image_dir));
            }

            $output_image_name = $output_image_dir
                 . $product_id
                 . '.'
                 . self::OUTPUT_FORMAT;
            $img->writeImage($output_image_name);

            if ($this->scenario_settings['test']) {
                break;
            }
        }

        $this->output = $this->params;
    }
}