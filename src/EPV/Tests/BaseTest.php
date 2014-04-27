<?php

namespace epv\Tests;


use epv\Tests\Exception\TestException;
use epv\Tests\Tests\Type;

abstract class BaseTest implements TestInterface {
    private $debug;
    protected  $fileTypeLine;
    protected  $fileTypeFull;

    /**
     * If this is set to true, tests are run on full directory listings.
     * @var bool
     */
    protected  $directory = false;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @throws Exception\TestException
     */
    public function validateLine()
    {
        throw new TestException("Test declared to be a line test, but doesn't implement validateLine");
    }

    /**
     * @throws Exception\TestException
     */
    public function validateFile()
    {
        throw new TestException("Test declared to be a file test, but doesn't implement validateFile");
    }

    /**
     * @param array $dirList
     * @return mixed|void
     * @throws Exception\TestException
     */
    public function validateDirectory(array $dirList)
    {
        throw new TestException("Test declared to be a directory listing test, but doesn't implement validateDirectory");
    }

    /**
     * @param int $type
     * @return bool
     */
    public function doValidateLine($type)
    {
        return $this->fileTypeLine & $type;
    }

    /**
     * @param int $type
     * @return bool
     */
    public function doValidateFile($type)
    {
        return $this->fileTypeFull & $type;
    }

    /**
     * @return bool
     */
    public function doValidateDirectory()
    {
        return $this->directory;
    }


    /**
     * Convert a boolean to Yes or No.
     *
     * @param $bool
     * @return string
     */
    private function boolToLang($bool)
    {
        return $bool ? "Yes" : "No";
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = 'Test: ' . $this->testName() . '. ';

        return $string;
    }
}