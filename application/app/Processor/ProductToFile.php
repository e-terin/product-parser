<?php

namespace App\Processor;

use App\Parser\ParserFactory;

class ProductToFile extends AbstractProcessor
{
    public function process()
    {
        // TODO: Implement process() method.
        $to = $this->settings['to'];
        $from  = $this->settings['from'] ?? 'Product';
        $this->settings['output_dir'] = $this->scenario_settings['dir_out'] . $this->scenario_settings['category'] . '/';

        $parser = ParserFactory::build($from, $to, $this->settings, $this->params);
        $this->output = $parser->parse()->getResult();
    }
}