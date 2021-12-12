<?php

namespace App\Command;

use App\Processor\ProcessorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class RunCommand extends Command
{
    protected static $defaultName = 'run';

    private mixed $input = null;
    private mixed $output = null;


    protected function configure()
    {
        $this
            // опции
            ->addOption(
                'scenario',
                's',
                InputOption::VALUE_REQUIRED,
                'Scenario',
                'default'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $scenario_filename = $_ENV['DIR_SCENARIO'] . $input->getOption('scenario') . '.yaml';
        $scenario = Yaml::parseFile($scenario_filename);

        foreach ($scenario['process'] as $process_name => $process_settings){

            try {
                // TODO make transmit only processor settings
                $processor = ProcessorFactory::build(
                    $process_name,
                    $process_settings,
                    $scenario['settings'],
                    $this->output); // результат работы предыдущего процессора отправляем в input следующего
                $processor->process();
            } catch (\Exception $e) {
                $output->writeln('Error: ' . $e->getMessage());
                return 1;
            }

            $this->output = $processor->getResult();

        }

        $output->writeln('Работу завершил');

        return 0;
    }
}