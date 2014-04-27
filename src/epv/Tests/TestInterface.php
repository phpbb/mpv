<?php

namespace epv\Tests;


use epv\Files\FileInterface;
use epv\Files\LineInterface;

interface TestInterface {
    /**
     * Validate a line in a specific file.
     * This method is only called if doValidateLine returns true.
     * @param \epv\Files\LineInterface $line Line to validate
     * @return
     */
    public function validateLine(LineInterface $line);

    /**
     * Validate a full file.
     * This method is only called if doValidateFile returns true.
     * @param \epv\Files\FileInterface $file
     * @return
     */
    public function validateFile(FileInterface $file);

    /**
     * Validate the directory listing.
     * This method is only called if doValidateDirectory returns true.
     * @param array $dirListing
     * @return mixed
     */
    public function validateDirectory(array $dirListing);

    /**
     * Check if this test should be runned for the directory listing.
     * @return boolean
     */
    public function doValidateDirectory();

    /**
     * Check if this test should be runned for each line.
     * @param $type int Filetype
     * @return boolean
     */
    public function doValidateLine($type);

    /**
     * Check if this test should be runned for the complete file
     * @param $type int Filetype
     * @return boolean
     */
    public function doValidateFile($type);


    /**
     *
     * @return bool
     */
    public function testName();
} 