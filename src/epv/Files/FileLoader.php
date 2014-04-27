<?php

namespace epv\Files;


use epv\Files\Exception\FileException;
use epv\Files\Type\ComposerFile;
use epv\Files\Type\HTMLFile;
use epv\Files\Type\JsonFile;
use epv\Files\Type\PHPFile;
use epv\Files\Type\PlainFile;
use epv\Files\Type\XmlFile;
use epv\Files\Type\YmlFile;
use epv\Output\Messages;
use epv\Output\OutputInterface;

class FileLoader {
    /**
     * @var \epv\Output\OutputInterface
     */
    private $output;
    private $debug;

    public function __construct(OutputInterface $output, $debug)
    {

        $this->output = $output;
        $this->debug = $debug;
    }

    public function loadFile($fileName)
    {
        $file = null;

        $split = explode('.', basename($fileName));
        $size = sizeof($split);

        if ($size == 1)
        {
            // File has no extension. If it is a readme file it is ok.
            // Otherwise add notice.
            if (strtolower($fileName) !== 'readme')
            {
                Messages::addMessage(Messages::NOTICE, sprintf("The file %s has no valid extension.", basename($fileName)));
            }
            $file = new PlainFixle($this->debug, $fileName);
        }
        else if ($size == 2)
        {
            $file = self::tryLoadFile($fileName, $split[1]);
        }
        else if ($size == 3)
        {
            // First, we tried the first extension,
            // Like phpunit-test.xml.all
            // If that has no matches, we try the
            // last extension.

            $file = self::tryLoadFile($fileName, $split[1]);

            if (!$file)
            {
                $file = self::tryLoadFile($fileName, $split[2]);
            }
        }
        else if ($size >= 4) // Files with 3 ore more dots should not happen.
        {

        }
        else // Blank filename?
        {
            throw new FileException("Filename was empty");
        }


        if ($file == null)
        {
            throw new FileException("Tried loading a unknown file");
        }

        return $file;
    }

    /**
     * Tries to load a file based on extension.
     *
     * In case of plaintext files, the contents is checked as well to see if it isn't a php file.
     *
     * @param $fileName
     * @param $extension
     * @return ComposerFile|HTMLFile|PHPFile|PlainFile|XmlFile|YmlFile|null
     */
    private function tryLoadFile($fileName, $extension)
    {
        $this->output->writelnIfDebug("<info>Trying to load $fileName with extension $extension</info>");

        switch (strtolower($extension))
        {
            case 'php':
                return new PHPFile($this->debug, $fileName);
            case 'html':
                return new HTMLFile($this->debug, $fileName);
            case 'json':
                if (strtolower(basename($fileName)) == 'composer.json')
                {
                    return new ComposerFile($this->debug, $fileName);
                }
                else
                {
                    return new JsonFile($this->debug, $fileName );
                }
            case 'yml':
                return new YmlFile($this->debug, $fileName);
            case 'txt':
            case 'md':
                return new PlainFile($this->debug, $fileName);
            case 'xml':
                return new XmlFile($this->debug, $fileName);
            default:
                $file = basename($fileName)
                Messages::addMessage(Messages::WARNING, "Can't detect type for file $file, handling it as binair file");
                return BinairFile($this->debug, $fileName);
        }
    }
} 