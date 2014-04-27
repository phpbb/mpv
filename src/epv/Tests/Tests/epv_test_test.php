<?php

namespace epv\Tests\Tests;

use epv\Files\LineInterface;
use epv\Tests\BaseTest;

class epv_test_test extends BaseTest{

    public function __construct($debug)
    {
        parent::__construct($debug);

        $this->fileTypeLine = Type::TYPE_PLAIN | Type::TYPE_SERVICE;
    }

    public function testName()
    {
        return "EPV test";
    }

    public function validateLine(LineInterface $line)
    {
    }
} 