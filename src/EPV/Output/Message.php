<?php
namespace epv\Output;


class Message {
    private $type;
    private $message;

    /**
     * @param $type int Type message
     * @param $message string Message
     */
    public function __construct($type, $message)
    {

        $this->type = $type;
        $this->message = $message;
    }

    public function __toString()
    {
        switch ($this->type)
        {
            case Messages::NOTICE:
                return "<info>Notice: $this->message</info>";
            case Messages::WARNING:
                return "<comment>Warning: $this->message</comment>";
            case Messages::ERROR:
                return "<question>Error: $this->message</question>";
            case Messages::FATAL:
                return "<error>Fatal: $this->message</error>";
        }
    }
} 