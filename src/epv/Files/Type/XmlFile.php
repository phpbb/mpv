<?php

namespace epv\Files\Type;

use epv\Tests\Tests\Type;
use epv\Files\BaseFile;

class XmlFile extends BaseFile implements XmlFileInterface{
    /**
     * Get the file type for the specific file.
     * @return int
     */
    function getFileType()
    {
        return Type::TYPE_XML;
    }
} 