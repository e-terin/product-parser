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
        $this->settings['input_dir'] = $_ENV['DIR_WORK'] . 'in/' . $this->settings['category'] . '/';

        $parser = ParserFactory::build($from, $to, $this->settings);
        $this->output = $parser->parse()->getResult();
    }
}