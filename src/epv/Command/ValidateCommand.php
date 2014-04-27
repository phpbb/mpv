<?php
/**
 * Created by PhpStorm.
 * User: paulsohier
 * Date: 26-04-14
 * Time: 23:41
 */

namespace epv\Command;

use epv\Output\Messages;
use epv\Output\Output;
use epv\Tests\TestRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends  Command{
    protected $debug;
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run the extension validator on your extension.')
            ->addArgument('dir', InputArgument::REQUIRED, 'The directory the extension is in.')
            ->addOption('debug', null, InputOption::VALUE_NONE, "Run in debug")

        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument("dir");
        $this->debug = $input->getOption("debug");

        $output = new Output($output, $this->debug);

        $output->writeln("Running Extension pre validator on directory <info>$dir</info>.");
        $runner = new TestRunner($input, $output, $dir, $this->debug);


        if ($this->debug)
        {
            $output->writeln("Tests to run: ");

            foreach ($runner->tests as $t => $test)
            {
                $output->writeln("<info>$test</info>");
            }
        }
        $runner->runTests();

        $output->writeln("<info>Test results for extension</info>");

        foreach (Messages::getMessages() as $msg)
        {
            $output->writeln((string)$msg);
        }

        if (Messages::getFatalCount() > 0)
        {
            return 1;
        }
        return 0;
    }
} 