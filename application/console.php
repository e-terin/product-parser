<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Command\RunCommand;

// TODO move to .env
$_ENV['DIR_BASE'] = __DIR__.'/';
$_ENV['DIR_WORK'] = $_ENV['DIR_BASE'].'work/';
$_ENV['DIR_SCENARIO'] = $_ENV['DIR_BASE'].'scenario/';

$application = new Application();

// ... register commands
//$application->add(new \App\Command\CreateUserCommand());
$application->add(new RunCommand());

$application->run();