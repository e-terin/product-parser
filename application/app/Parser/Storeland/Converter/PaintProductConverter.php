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

            $paint_code = $this->findPaintNumber($product);
            if ($paint_code){
                $product->addAttribute('paint_code', $paint_code);
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

    private function findPaintNumber(Product $product): string
    {
        // если определено свойство - берем из него
        // todo а где свойства то ? добавить!
        if (isset($product->properties['Код краски по VIN номеру (заводскому коду краски)'])
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