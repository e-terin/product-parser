<?php

namespace App\Command;

use App\Processor\ProcessorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Dotenv\Dotenv;

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

        $dotenv = new Dotenv();
        $dotenv->load($_ENV['DIR_BASE'].'/.env');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $scenario_filename = $_ENV['DIR_BASE'] . $_ENV['DIR_SCENARIO'] . $input->getOption('scenario') . '.yaml';
        $scenario = Yaml::parseFile($scenario_filename);

        $scenario_name = $scenario['name'];

        $scenario['settings']['dir_in'] = $_ENV['DIR_BASE'] . $_ENV['DIR_WORK'] . 'in/';
        $scenario['settings']['dir_out'] = $_ENV['DIR_BASE'] . $_ENV['DIR_WORK'] . 'out/';

        $output->writeln("Scenario {$scenario_name} start");
        $output->writeln('______________________');

        $count_process = count($scenario['process']);
        $counter_process = 0;

        foreach ($scenario['process'] as $process){

            $process_name = $process['name'] ?? null;

            if (!$process_name) {
                throw new \RuntimeException('Attribute \"name"\ is required in process scenario');
            }

            $process_settings = $process['settings'] ?? null;

            $counter_process++;

            $skip_process = $process['skip'] ?? false;

            if ($skip_process) {
                $output->writeln("Skip {$counter_process}/{$count_process } process: {$process_name}");
                $output->writeln('______________________');
                continue;
            }

            $output->writeln("Start {$counter_process}/{$count_process } process: {$process_name}");
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
            $output->writeln("Process {$process_name} has been completed successfully");
            $output->writeln('______________________');

        }

        $output->writeln("Scenario {$scenario_name} end successful");

        return 0;
    }
}