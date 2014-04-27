<?php

namespace epv\Tests\Tests;

use epv\Output\Messages;
use epv\Tests\BaseTest;

class epv_test_validate_directory_structure  extends BaseTest{
    public function __construct($debug)
    {
        parent::__construct($debug);

        $this->directory = true;

    }

    public function validateDirectory(array $dirList)
    {
        $requiredFiles = array(
            'license.txt',
            'composer.json',
            'ext.php',
        );

        foreach ($requiredFiles as $file)
        {
            // TODO: Depending on the specs for extensions.
            $found = false;

            foreach ($dirList as $dir)
            {
                if (basename($dir) == $file)
                {
                    $found = true;
                    break;
                }
            }
            if (!$found)
            {
                Messages::addMessage(Messages::ERROR, sprintf("The required file %s is missing in the extension package.", $file));
            }
        }
    }

    public function testName()
    {
        return "Validate directory structure";
    }
}