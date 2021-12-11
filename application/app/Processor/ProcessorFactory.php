<?php

namespace App\Processor;

class ProcessorFactory
{
    public static function build(string $name, array $settings, array $scenario_settings, mixed $params)
    {
        $processor = '\\App\\Processor\\' . ucfirst($name);

        if (class_exists($processor)) {
            return new $processor($settings, $scenario_settings, $params);
        }

        throw new \RuntimeException('I can`t find such processor ' . $name);
    }
}