<?php

namespace epv\Files\Type;

use epv\Files\BaseFile;
use epv\Tests\Tests\Type;

class LangFile extends BaseFile implements LangFileInterface{
    /**
     * Get the file type for the specific file.
     * @return int
     */
    function getFileType()
    {
        return Type::TYPE_LANG | Type::TYPE_PHP;
    }
} 