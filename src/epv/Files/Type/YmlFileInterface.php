<?php

namespace epv\Files\Type;


use epv\Files\FileInterface;

interface YmlFileInterface extends FileInterface{
    /**
     * Get a array with the data in the yaml file.
     *
     * @return array parsed yaml file
     */
    public function getYaml();
} 