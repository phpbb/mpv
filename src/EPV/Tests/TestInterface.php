<?php

namespace epv\Tests;


interface TestInterface {
    /**
     * Validate a line in a specific file.
     * This method is only called if doValidateLine returns true.
     */
    public function validateLine();
    /**
     * Validate a full file.
     * This method is only called if doValidateFile returns true.
     */
    public function validateFile();

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