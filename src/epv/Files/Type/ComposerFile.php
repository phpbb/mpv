<?php

namespace epv\Files\Type;

use epv\Tests\Tests\Type;

class ComposerFile extends JsonFile implements ComposerFileInterface{
    /**
     * Get the file type for the specific file.
     * @return int
     */
    function getFileType()
    {
        return Type::TYPE_COMPOSER | Type::TYPE_JSON;
    }
} 