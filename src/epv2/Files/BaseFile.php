<?php

namespace epv\Files;


use epv\Files\Exception\FileException;

class BaseFile implements FileInterface {

    private $fileName;
    private $fileData;
    private $fileArray;
    private $debug;

    /**
     * @param $debug Debug Mode
     * @param $fileName filename for this file
     * @throws Exception\FileException
     */
    public function __construct($debug, $fileName)
    {
        if (!file_exists($fileName))
        {
            throw new FileException("File ({$fileName}) couldn't be found");
        }
        $this->debug = $debug;
        $this->fileName = $fileName;
        $this->fileData = @file_get_contents($this->fileName);

        if ($this->fileData === false)
        {
            throw new FileException("Unable to read file {$fileName}.");
        }
        $this->fileArray = explode("\n", $this->fileData);
    }
} 