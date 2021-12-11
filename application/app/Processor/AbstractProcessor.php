<?php

namespace App\Processor;

use ReflectionClass;

abstract class AbstractProcessor
{
    protected mixed $input             = null;
    protected mixed $output            = null;
    protected mixed $settings          = null;
    protected mixed $scenario_settings = null;
    protected mixed $params            = null;

    public function __construct($settings, $scenario_settings, $params)
    {
        $this->settings = $settings;
        $this->scenario_settings = $scenario_settings;
        $this->params = $params;
    }

    public function getResult()
    {
        return $this->output;
    }
    abstract public function process();
}