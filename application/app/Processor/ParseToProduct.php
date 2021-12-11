<?php

namespace App\Processor;

use App\Parser\ParserFactory;

class ParseToProduct extends AbstractProcessor
{
    public function process()
    {
        // TODO: Implement process() method.
        $from = $this->settings['from'];
        $to = 'Product';
        $settings = [
            'format'   => $this->settings['format'],
            'category' => $this->settings['category'],
            'input_dir' => $_ENV['DIR_WORK'] . 'in/' . $this->settings['category'] . '/',
        ];

        $parser = ParserFactory::build($from, $to, $settings);
        return $parser->process()->getResult();
    }
}