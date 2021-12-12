<?php

namespace App\Domain;

use App\DataProvider\MarkiDataProvider;

class PaintProduct extends Product
{
	protected string $brand_car;
	protected string $paint_number;

	public function __construct(array $params)
	{
		parent::__construct($params);

		$this->brand_car = $this->findBrandCar();
		$this->paint_number = $this->findPaintNumber($params);
	}

	/**
	 * Ищем марку краски в названии
	 * @return string
	 */
	private function findBrandCar(): string
	{
		foreach (MarkiDataProvider::MARKI as $brand) {
			if (strpos($this->name, 'на '.$brand)) {
				return $brand;
			}
		}

		return '';
	}

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