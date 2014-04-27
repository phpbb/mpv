<?php

namespace epv\Files;


interface FileInterface {
    /**
     * Get the file type for the specific file.
     * @return int
     */
    function getFileType();

    /**
     * Get a array of lines for this specific file.
     * @return array
     */
    function getLines();

    /**
     * Get the filename for this file.
     * @return string
     */
    function getFilename();
} 