<?php

namespace epv\Files\Type;

use epv\Tests\Tests\Type;
use epv\Files\BaseFile;

class PHPFile extends BaseFile implements PHPFileInterface{
    /**
     * Get the file type for the specific file.
     * @return int
     */
    function getFileType()
    {
        return Type::TYPE_PHP;
    }
} 