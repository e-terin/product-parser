<?php

namespace App\Parser;

class ParserFactory
{
    public static function build(string $from, string $to, mixed $settings = null, mixed $params = null)
    {
        if ($to === 'Product') { // todo remake to constant
            $parser = '\\App\Parser\\' . ucfirst($from) . '\\' . ucfirst($from) . ucfirst($to);
        } else {
            $parser = '\\App\Parser\\' . ucfirst($to) . '\\' . ucfirst($from) . ucfirst($to);
        }

        if (class_exists($parser)) {
            return new $parser($settings, $params);
        }
        throw new \RuntimeException('Can`t find parser : ' . $parser);
    }
}