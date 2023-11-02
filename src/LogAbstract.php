<?php

namespace LogReader;

use SplFileObject;

abstract class LogAbstract {

    /**
     *
     * @var SplFileObject
     */
    protected $_file;

    /**
     *
     * @var string
     */
    protected $_filename;

    /**
     *
     * @var Storage\LogInterface
     */
    protected $_storage;

    public function __construct($filename = '', $storage = null) {
        if ($filename) {
            $this->setFile($filename);
        }
        if ($storage) {
            $this->setStorage($storage);
        }
    }

    public function setFile($filename) {
        if (!file_exists($filename)) {
            throw new Exception("File '$filename' does not exist");
        }
        if (!is_file($filename)) { # you can disable this condition if it sends false positives
            throw new Exception("File '$filename' is not a regular file.");
        }
        if (!is_readable($filename)) { # you can disable this condition if it sends false positives
            throw new Exception("File '$filename' is not readable");
        }
        $this->_filename = $filename;

        $this->_file = new SplFileObject($filename);
    }

    public function setStorage(Storage\LogInterface $storage) {
        $this->_storage = $storage;
    }

    abstract public function read();


    /**
     *
     * @return Storage\LogInterface
     */
    public function getStorage(): Storage\LogInterface
    {
        return $this->_storage;
    }
    
    /**
     * 
     * @return string
     */
    public function getFilename(): string
    {
        return $this->_filename;
    }
}