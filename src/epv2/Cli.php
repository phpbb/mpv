<?php

namespace epv;

use epv\Command\ValidateCommand;
use Symfony\Component\Console\Application;

class Cli extends Application {

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new ValidateCommand();
        return $commands;
    }
} 