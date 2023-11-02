<?php

namespace LogReader;

abstract class LogAbstract {

    /**
     *
     * @var \SplFileObject
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
        $this->_filename = $filename;
        if (!is_file($filename)) {
            throw new Exception("File '$filename' does not exist");
        }
        if (!is_readable($filename)) {
            throw new Exception("File '$filename' is not readable");
        }
        $this->_file = new \SplFileObject($filename);
    }

    public function setStorage(Storage\LogInterface $storage) {
        $this->_storage = $storage;
    }

    abstract public function read();


    /**
     *
     * @return Storage\LogInterface
     */
    public function getStorage() {
        return $this->_storage;
    }
    
    /**
     * 
     * @return string
     */
    public function getFilename() {
        return $this->_filename;
    }
}