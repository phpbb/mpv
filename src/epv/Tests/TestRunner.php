<?php

namespace epv\Tests;


use epv\Files\FileLoader;
use epv\Files\Line;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use epv\Tests\Exception\TestException;
use epv\Output\OutputInterface;

class TestRunner
{
    public $tests = array();
    private $files = array();
    private $dirList = array();
    private $input;
    private $output;
    private $directory;
    private $debug;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $directory The directory where the extension is located.
     * @param $debug Debug mode.
     */
    public function __construct(InputInterface $input, OutputInterface $output, $directory, $debug)
    {
        $this->input = $input;
        $this->output = $output;
        $this->directory = $directory;
        $this->debug = $debug;

        $this->loadTests();
        $this->loadFiles();
    }

    /**
     * Run the actual test suite.
     *
     * @throws Exception\TestException
     */
    public function runTests()
    {
        if (sizeof($this->tests) == 0)
        {
            throw new TestException("TestRunner not initialised");
        }
        $this->output->writeln("Running tests");

        // First, do all tests that want a directory listing.
        // All other tests are specific to files.

        foreach ($this->tests as $test)
        {
            if ($test->doValidateDirectory())
            {
                $test->validateDirectory($this->dirList);
            }
        }

        // Time to loop over the files in memory,
        // And over the tests that are available.
        // First do the full file check.
        // After that loop over each line and test per line.
        foreach ($this->files as $file)
        {
            $linetest = array();

            foreach ($this->tests as $test)
            {
                if ($test->doValidateFile($file->getFileType()))
                {
                    $test->validateFile($file);
                }

                // To prevent looping over too many tests, we check here if we need to loop
                // over tests for line by line tests.
                if ($test->doValidateLine($file->getFileType()))
                {
                    $linetest[] = $test;
                }
            }

            if (sizeof ($linetest))
            {
                $linenr = 1;
                foreach ($file->getLines() as $line)
                {
                    $runline = new Line($file, $linenr, $line);
                    foreach ($linetest as $test)
                    {
                        $test->validateLine($runline);
                    }
                    $linenr++;
                }
            }
        }
    }

    /**
     * Load all files from the extension.
     */
    private function loadFiles()
    {
        $finder = new Finder();

        $iterator = $finder
            ->files()
            ->name('*')
            ->in($this->directory);

        $loader = new FileLoader($this->output, $this->debug);
        foreach ($iterator as $file)
        {
            $this->files[] = $loader->loadFile($file->getRealPath());
            $this->dirList[] = str_replace($this->directory, '', $file->getRealPath());
        }
    }

    /**
     * Load all available tests.
     */
    private function loadTests()
    {
        $finder = new Finder();

        $iterator = $finder
            ->files()
            ->name('epv_test_*.php')

            ->size(">= 0K")
            ->in(__DIR__ . '/Tests');

        foreach ($iterator as $test)
        {
            $this->tryToLoadTest($test);
        }
    }

    /**
     * Try to load and initialise a specific test.
     * @param SplFileInfo $test
     * @throws Exception\TestException
     */
    private function tryToLoadTest(SplFileInfo $test)
    {
        $this->output->writelnIfDebug("<info>Got {$test->getRealpath()}.</info>");
        $file = str_replace('.php', '', basename($test->getRealPath()));

        $class = '\\epv\\Tests\\Tests\\' . $file;

        $filetest = new $class($this->debug);

        if (!$filetest instanceof TestInterface)
        {
            throw new TestException("$class doesn't implement the TestInterface, but matches the test expression");
        }
        $this->tests[] = $filetest;

    }
} 