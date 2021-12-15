<?php

namespace App\Parser\Storeland;

use App\Collection\ProductCollection;
use App\DataProvider\FilesDataProvider;
use App\Domain\Product;
use App\Parser\AbstractParser;
use App\Parser\ConverterFactory;
use League\Csv\Exception;
use RuntimeException;

/**
 *
 */
class ProductStoreland extends AbstractParser
{
    private ProductCollection $products;

    /**
     * Парсим файл выгрузки Storeland в коллекцию Product и сохраняем ее в $this->output
     *
     * @return array
     * @throws Exception
     */
    public function parse(): ProductStoreland
    {

       // $this->output = $this->fillProducts();

        //

        $products = $this->params->getCollection();

        if ($products->isEmpty()) {
            throw new RuntimeException('Products is empty');
        }

        $rows = [];

        $rows[] = [
            'Название товара',
            'Артикул',
            'Цена продажи, без учёта скидок',
            'Остаток',
            'Изображения товара',
            'Описание модификации товара',
            'Идентификатор товара в магазине',
            'Изображение модификации товара',
        ];

        foreach ($products as $product_id => $product) {
            $modifications = $product->modifications;

            // выстраиваем в ряд свойства модификаций (properties) проверяем их количество - хз надо или нет
            // считаем их
            // формируем заголовок
            $properties_vector = [];

            foreach ($modifications as $modification){
                // у каждой модификации есть свой-ва
                $properties_vector = [];

                foreach ($modification['properties'] as $property) {
                    $properties_vector [] = $property['name'];
                    $properties_vector [] = $property['value'];
                }

                $rows[] = array_merge(
                [
                    $product->name, //Название товара
                    $product->code, //Артикул
                    $modification['price'], //Цена продажи, без учёта скидок
                    $product->stock, // Остаток
                    implode(PHP_EOL, $product->images), //Изображения товара
                    $modification['desc'], // Описание модификации товара
                    $product->id, // Идентификатор товара в магазине
                    $modification['image'], // Изображение модификации товара
                ],
                $properties_vector);
            }

        }

        // add column to header
        $count_modification_property_pair = count($properties_vector)/2;
        $characteristic_vector = [];
        for ($i = 1; $i <= $count_modification_property_pair; $i++) {
            // todo
            $characteristic_vector[] = 'Название х-ки товара №' . $i;
            $characteristic_vector[] = 'Значение х-ки товара №' . $i;
        }
        $rows[0] = array_merge($rows[0],$characteristic_vector);

        $filename = $this->settings['to'] . '.' . $this->settings['format'];
        (new FilesDataProvider($this->settings['output_dir']))->saveArrayToCsv($rows, $filename);

        return $this;
    }
}