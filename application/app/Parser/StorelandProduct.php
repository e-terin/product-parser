<?php

namespace App\Parser;

use App\DataProvider\FilesDataProvider;
use App\Domain\Product;

class StorelandProduct extends AbstractParser
{

    public function process(): array
    {
        $filesDP = new FilesDataProvider($this->settings['input_dir']);
        $csv_files = $filesDP->getCsvFiles();
        $qty_csv_file = count($csv_files);

        if($qty_csv_file === 0) {
            throw new \RuntimeException('There are no CSV files to parse in folder ' . $this->settings['input_dir']);
        }

        return [];
    }
    protected function fillProducts()
    {
        // Получаем файлы csv в папке
        $filesDP = new FilesDataProvider($this->category);
        $csv_files = $filesDP->getCsvFilesName();
        $qty_csv_file = count($csv_files);

        // Перебираем файлы
        $counter_files = 0;
        foreach ($csv_files as $csv_file)
        {
            $counter_files++;
            $this->output->writeln('______________________________');
            $this->output->writeln('Начинаю обрабатывать файл '
                                   . $counter_files
                                   . ' из '
                                   . $qty_csv_file
                                   . ' '
                                   . basename($csv_file));
            $csv = $filesDP->getCsvFile($csv_file);
            $rows = $csv['records'];

            // перебираем строки
            $prev_product_id = '';

            foreach ($rows as $row)
            {
                $product_id = $row['Идентификатор товара в магазине'] ?? null;

                // делаем что-то только если новый товар - модификации пропускаем
                if($prev_product_id != $product_id)
                {
                    $product = new Product($row);
                    $this->products->put($product_id,$product);
                }
                elseif ($this->is_fill_modification)
                {
                    $product = $this->products->get($product_id);
                    $product->addModification($row);
                    $this->products->put($product_id,$product);
                }

                // запоминаем какой товар обрабатывали
                $prev_product_id = $product_id;
            }
        }
    }
}