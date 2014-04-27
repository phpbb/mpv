<?php

namespace epv\Files\Type;

use epv\Tests\Tests\Type;
use epv\Files\BaseFile;

class PlainFile extends BaseFile implements PlainFileInterface{
    /**
     * Get the file type for the specific file.
     * @return int
     */
    function getFileType()
    {
        return Type::TYPE_PLAIN;
    }
} 