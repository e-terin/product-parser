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
        $row = [];
        foreach ($products as $product_id => $product) {
            $modifications = $product->modifications;

            // выстраиваем в ряд свойства модификаций (properties) проверяем их количество - хз надо или нет
            // считаем их
            // формируем заголовок

            foreach ($modifications as $modification){
                $row[] = [
                    $product->name,
                    $product->code,

                ];
            }
        }




        // если указана категория товара и для нее есть спец обработка
        $category = $this->settings['category'] ?? null;
        if ($category){
            $converter = ConverterFactory::build($this->settings['from'], $this->settings['category'] , $this->output);
            $this->output = $converter
                ->convert()
                ->getResult();
        }
        return $this;
    }

    /**
     * Заполняем $products данными из файла
     * @return ProductCollection
     * @throws Exception
     */
    protected function fillProducts(): ProductCollection
    {
        $products = new ProductCollection();

        $filesDP = new FilesDataProvider($this->settings['input_dir']);
        $csv_files = $filesDP->getCsvFiles();
        $qty_csv_file = count($csv_files);

        if ($qty_csv_file === 0) {
            throw new RuntimeException('There are no CSV files to parse in folder ' . $this->settings['input_dir']);
        }

        // Перебираем файлы
        $counter_files = 0;

        foreach ($csv_files as $csv_file) {
            $counter_files++;
            // TODO: remake stdout to output object
            echo('Начинаю обрабатывать файл '
                 . $counter_files
                 . ' из '
                 . $qty_csv_file
                 . ' '
                 . basename($csv_file)
                 . PHP_EOL);

            $csv = $filesDP->getCsvFile($csv_file);
            $rows = $csv['rows'];

            // перебираем строки
            $prev_product_id = '';

            foreach ($rows as $row) {
                $product_id = $row['Идентификатор товара в магазине'] ?? null;

                if ($prev_product_id != $product_id) {
                    $product = new Product($row);
                    $product->addModification($row); // продукт всегда содержит как минимум одну модификацию
                    $products->put($product_id, $product);
                } elseif ($this->settings['modification']) {
                    $product = $products->get($product_id);
                    $product->addModification($row);
                    $products->put($product_id, $product);
                }

                // запоминаем какой товар обрабатывали
                $prev_product_id = $product_id;
            }
        }

        return $products;
    }
}