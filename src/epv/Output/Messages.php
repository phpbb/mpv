<?php
/**
 * Created by PhpStorm.
 * User: paulsohier
 * Date: 27-04-14
 * Time: 14:38
 */

namespace epv\Output;


use epv\Files\FileInterface;

class Messages {
    private static $messages = array();
    private static $fatal;
    private static $error;
    private static $warning;
    private static $notice;

    const FATAL = 4;
    const WARNING = 3;
    const ERROR = 2;
    const NOTICE = 1;

    /**
     * Add a new message to the output of the validator.
     *
     * @param $type int message type
     * @param $message string message.
     * @param \epv\Files\FileInterface $file
     */
    public static function addMessage($type, $message, FileInterface $file = null)
    {
        switch ($type)
        {
            case self::FATAL:
                self::$fatal++;
            break;
            case self::ERROR:
                self::$error++;
            break;
            case self::WARNING:
                self::$warning++;
            break;
            case self::NOTICE:
                self::$notice++;
            break;
            default:
                // TODO: Decide on this?
        }
        self::$messages[] = new Message($type, $message, $file);
    }

    /**
     * Get all messages saved into the message queue.
     * @return array Array with messages
     */
    public static function getMessages()
    {
        return self::$messages;
    }

    /**
     * Get the amount of messages that were fatal.
     * @return int
     */
    public static function getFatalCount()
    {
        return self::$fatal;
    }
} 