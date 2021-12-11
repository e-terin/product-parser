<?php

namespace App\Parser;

class ParserFactory
{
    public static function build(string $from, string $to, mixed $settings)
    {
        $parser = '\\App\Parser\\' . ucfirst($from) . ucfirst($to);

        if (class_exists($parser)) {
            return new $parser($settings);
        }
        throw new \RuntimeException('Can`t find parser : ' . $parser);
    }
}