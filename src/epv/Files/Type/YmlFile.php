<?php

namespace epv\Files\Type;


use epv\Files\BaseFile;
use epv\Output\Messages;
use epv\Tests\Tests\Type;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YmlFile extends BaseFile implements YmlFileInterface
{
    protected $yamlFile;

    public function __construct($debug, $filename)
    {
        parent::__construct($debug, $filename);

        try
        {
            $this->yamlFile = Yaml::parse($this->fileData);
        }
        catch (ParseException $ex)
        {
            Messages::addMessage(Messages::WARNING, "Parsing yaml file ($filename) failed: " . $ex->getMessage());
        }
    }

    /**
     * Get a array with the data in the yaml file.
     *
     * @return array
     */
    public function getYaml()
    {
        return $this->yamlFile;
    }

    /**
     * Get the file type for the specific file.
     * @return int
     */
    function getFileType()
    {
        return Type::TYPE_YML;
    }
}