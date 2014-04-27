<?php

namespace epv\Tests\Tests;

use epv\Tests\BaseTest;

class epv_test_test extends BaseTest{

    public function __construct($debug)
    {
        parent::__construct($debug);

        $this->fileTypeLine = Type::TYPE_PHP | Type::TYPE_PLAIN;

    }

    public function testName()
    {
        return "EPV test";
    }
} 