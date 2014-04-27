<?php

namespace epv\Output;


use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Output implements \epv\Output\OutputInterface {

    private $output;
    private $debug;
    public function __construct(OutputInterface $output, $debug)
    {
        $this->output = $output;
        $this->debug = $debug;
    }

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param Boolean $newline Whether to add a newline
     * @param integer $type The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @api
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        return $this->output->write($messages, $newline, $type);
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param integer $type The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @api
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        return $this->output->writeln($messages, $type);
    }

    /**
     * Sets the verbosity of the output.
     *
     * @param integer $level The level of verbosity (one of the VERBOSITY constants)
     *
     * @api
     */
    public function setVerbosity($level)
    {
        return $this->output->setVerbosity($level);
    }

    /**
     * Gets the current verbosity of the output.
     *
     * @return integer The current level of verbosity (one of the VERBOSITY constants)
     *
     * @api
     */
    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }

    /**
     * Sets the decorated flag.
     *
     * @param Boolean $decorated Whether to decorate the messages
     *
     * @api
     */
    public function setDecorated($decorated)
    {
        return $this->output->setDecorated($decorated);
    }

    /**
     * Gets the decorated flag.
     *
     * @return Boolean true if the output will decorate messages, false otherwise
     *
     * @api
     */
    public function isDecorated()
    {
        return $this->output->isDecorated();
    }

    /**
     * Sets output formatter.
     *
     * @param OutputFormatterInterface $formatter
     *
     * @api
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        return $this->output->setFormatter($formatter);
    }

    /**
     * Returns current output formatter instance.
     *
     * @return  OutputFormatterInterface
     *
     * @api
     */
    public function getFormatter()
    {
        return $this->output->getFormatter();
    }

    /**
     * Write a message to the output, but only if Debug is enabled.
     *
     * @param $message string|array $messages The message as an array of lines of a single string
     *
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function writelnIfDebug($message)
    {
        if ($this->debug)
        {
            $this->writeln($message);
        }
    }
}