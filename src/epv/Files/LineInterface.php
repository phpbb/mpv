<?php

namespace epv\Files;


interface LineInterface {
    /**
     * Get the file for this specific line
     * @return FileInterface
     */
    public function getFile();

    public function getLineNr();

    public function getLine();
} 