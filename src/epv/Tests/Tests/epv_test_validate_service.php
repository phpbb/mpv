<?php
/**
 * Created by PhpStorm.
 * User: paulsohier
 * Date: 27-04-14
 * Time: 20:24
 */

namespace epv\Tests\Tests;


use epv\Tests\BaseTest;

class epv_test_validate_service extends BaseTest {


    public function __construct($debug)
    {
        parent::__construct($debug);

        $this->fileTypeFull = Type::TYPE_SERVICE;
    }

    public function validateFile()
    {

    }

    /**
     *
     * @return String
     */
    public function testName()
    {
        return "Validate service";
    }

} 