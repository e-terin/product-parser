<?php

namespace App\Parser;

/**
 * Парсер берет данные из input и используя settings помещает их в output
 */
abstract class AbstractParser
{
    //protected mixed $input             = null;
    protected mixed $output            = null;
    protected mixed $settings          = null;
    protected mixed $params            = null;

    public function __construct($settings, $params = null)
    {
        $this->settings = $settings;
        $this->params = $params;
    }

    public function getResult()
    {
        return $this->output;
    }

    abstract public function parse(): AbstractParser;

}