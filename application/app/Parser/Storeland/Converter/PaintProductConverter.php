<?php

namespace App\Parser\Storeland\Converter;

use App\DataProvider\MarkiDataProvider;
use App\Domain\Product;
use App\Parser\AbstractProductConverter;

class PaintProductConverter extends AbstractProductConverter
{

    public function convert()
    {
        // TODO: Implement convert() method.
        $this->output = clone $this->input;
        $this->output->map(function($id, $product) {

            $brand_car = $this->findBrandCar($product);
            if ($brand_car){
                $product->addAttribute('brand_car', $brand_car);
            }
            return true; });
        return $this;
    }

    /**
     * Ищем марку краски в названии
     * @return string
     */
    private function findBrandCar(Product $product): string
    {
        foreach (MarkiDataProvider::MARKI as $brand_car) {
            if (strpos($product->name, 'на '.$brand_car)) {
                return $brand_car;
            }
        }

        return '';
    }

    // TODO: сделать когда будем делать подкраски
    private function findPaintNumber(): string
    {
        // если определено свойство - берем из него
        if (isset($this->properties['Код краски по VIN номеру (заводскому коду краски)'])
            && !empty($this->properties['Код краски по VIN номеру (заводскому коду краски)'])){
            return $this->properties['Код краски по VIN номеру (заводскому коду краски)'];
        }

        // Если свой-ва нет - ищем в артикуле
        // Готовая краска в банках на Audi - AULZ5R
        if (isset($this->code) 	&& !empty($this->code)){
            $code_components = explode('-',$this->code);

            return
                isset($code_components[1]) && !empty($code_components[1])
                    ? trim($code_components[1])
                    : '';
        }

        return '';
    }
}