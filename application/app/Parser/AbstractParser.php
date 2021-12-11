<?php

namespace App\Parser;

abstract class AbstractParser
{
    protected mixed $input             = null;
    protected mixed $output            = null;
    protected mixed $settings          = null;
    protected mixed $params            = null;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function getResult()
    {
        return $this->output;
    }

    abstract public function process(): array;

}