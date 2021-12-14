<?php

namespace App\Domain;

// todo класс заточен только под стореленд! Вынести в абстракцию!
class Product
{
	protected string $name;
	protected string $desc;
	protected string $id;
	protected string $code;
	protected string $price;
	protected string $stock;
	protected array  $images;
	protected string $url;
	protected array $properties;
	protected array $modifications;
    /**
     * Аттрибуты специфичны для каждой категории товаров
     * @var array
     */
    protected array $attributes;

	// список полей, которые берутся прямо из файла без обработки
	public const PARAM_NAME = [
		'Название товара'                 => 'name',
		'Полное описание товара'          => 'desc',
		'Артикул'                         => 'code',
		'Цена продажи, без учёта скидок'  => 'price',
		'Остаток'                         => 'stock',
		'URL переменная пути'             => 'url',
		'Идентификатор товара в магазине' => 'id',
	];

	/**
     * Создает товар из строчки товара
	 * @param $name
	 */
	public function __construct(array $params)
	{
		if (empty($params)) {
			return;
		}

		foreach ($params as $param_name => $param_value) {

			// поля, которые просто копируем из файла
            if (array_key_exists($param_name, self::PARAM_NAME)){
				$property_name = self::PARAM_NAME[$param_name];
				$this->$property_name = $param_value;
				continue;
			}

			if ($param_name === 'Изображения товара')			{
				$this->setImages($param_value);
				continue;
			}

			if (str_contains($param_name, 'Название х-ки товара №')){
				$number_product_property=mb_substr($param_name, 22);

				$field_value = 'Значение х-ки товара №' . $number_product_property;
				$field_name = 'Название х-ки товара №' . $number_product_property;

				if (isset($params[$field_value])
					&& !empty($params[$field_value])) {
					$this->properties[$params[$field_name]] =
						$params[$field_value];
				}
			}
		}
	}

	// картинки добавляем списком

	/**
	 * @param string $images
	 */
	protected function setImages(string $images): void
	{
		$this->images = explode("\n", $images);
	}

	/**
	 * @param array $modification
	 */
	public function addModification(array $row): void
	{
        $properties = [];

        $i = 1;
        while (array_key_exists("Название св-ва для модификации товара №{$i}", $row))
        {
            $properties[] =
                [
                    'name' => $row["Название св-ва для модификации товара №{$i}"],
                    'value' => $row["Значение св-ва для модификации товара №{$i}"],
                ];
            $i++;
        }

        $modification = [
            'desc' => $row['Описание модификации товара'],
            'image' => $row['Изображение модификации товара'],
            'price' => $row['Цена продажи, без учёта скидок'],
            'properties' => $properties,
        ];

        $this->modifications[] = $modification;
	}

    public function addAttribute($name,$value): void
    {
        $this->attributes[$name] = $value;
    }

    public function replaceImage($number, $value): void
    {
        if (isset($this->images[$number])) {
            $this->images[$number] = $value;
        }
    }



	public function __get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		}
		else return 'Ошибкос! Не найден ' . $property;
	}

	public function __set($name, $value): void
	{
	}
	public function __isset($name): bool
	{
		return isset($this->$name);
	}

}