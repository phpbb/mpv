<?php

namespace epv\Output;


interface OutputInterface extends \Symfony\Component\Console\Output\OutputInterface {

    /**
     * Write a message to the output, but only if Debug is enabled.
     *
     * @param $message string|array $messages The message as an array of lines of a single string
     *
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function writelnIfDebug($message);
}