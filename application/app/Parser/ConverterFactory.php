<?php

namespace App\Parser;

class ConverterFactory
{
    public static function build(string $service, string $category, $data)
    {
        $converter = '\\App\Parser\\' . ucfirst($service) .'\\Converter\\' . ucfirst($category) . 'ProductConverter'; // слишком частно

        if (class_exists($converter)) {
            return new $converter($data);
        }
        throw new \RuntimeException('Can`t find converter : ' . $converter);
    }
}