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
class PaintStoreland extends AbstractParser
{
    private ProductCollection $products;

    /**
     * Парсим файл выгрузки Storeland в коллекцию Product и сохраняем ее в $this->output
     *
     * @return PaintStoreland
     * @throws Exception
     */
    public function parse(): PaintStoreland
    {

        $products = $this->params->getCollection();

        if ($products->isEmpty()) {
            throw new RuntimeException('Products is empty');
        }

        $rows = [];

        $header = [
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

            if(empty($product->attributes['brand_car'])){
                throw new \RuntimeException('Property brand car required in product #' . $product_id);
            }

            $modifications = $product->modifications;

            // выстраиваем в ряд свойства модификаций (properties) проверяем их количество - хз надо или нет
            // считаем их
            // формируем заголовок
            $properties_vector = [];
            $mod_qty = 0;
            foreach ($modifications as $modification){
                // у каждой модификации есть свой-ва
                $properties_vector = [];

                foreach ($modification['properties'] as $property) {
                    $properties_vector [] = $property['name'];
                    $properties_vector [] = $property['value'];
                }

                $rows[$product->attributes['brand_car']]['data'][] = array_merge(
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
                $mod_qty++;
            }

        }

        // add column to header
        $count_modification_property_pair = count($properties_vector)/2;
        $characteristic_vector = [];
        for ($i = 1; $i <= $count_modification_property_pair; $i++) {
            // todo
            $characteristic_vector[] = 'Название св-ва для модификации товара №' . $i;
            $characteristic_vector[] = 'Значение св-ва для модификации товара №' . $i;
        }
        $header = array_merge($header,$characteristic_vector);

        foreach ($rows as $marka=>$data){
            $rows[$marka]['header'] = $header;
            $rows[$marka]['mod_qty'] = $mod_qty;
        }

        //$rows = $this->clearFields($rows, $this->settings['clear_fields'] ?? null);

        (new FilesDataProvider($this->settings['output_dir']))->saveFileToCsvByMarka($rows);

        return $this;
    }

    /**
     * Очищает указанные поля. Массив $rows должен обязательно содержать заголовок с названиями полей
     * @param array $rows
     * @param $clear_fields
     * @return array
     */
    private function clearFields(array $rows, $clear_fields = null): array
    {
        if(!is_array($clear_fields) || empty($clear_fields)){
            return $rows;
        }

        // find index to delete
        $fields_to_clear_array = array_filter($rows[0], static function($column) use ($clear_fields){
            return in_array($column, $clear_fields, true);
        });

        $fields_to_clear = array_keys($fields_to_clear_array);

        $new_rows = [];
        $is_header = true;
        foreach ($rows as $row) {
            if ($is_header) {
                $is_header = false;
                $new_rows[] = $row;
                continue;
            }

            array_walk($row, static function (&$field, $index) use ($fields_to_clear) {
                if(in_array($index, $fields_to_clear)){
                    $field = '';
                }
            });

            $new_rows[] = $row;
        }

        return $new_rows;
    }
}