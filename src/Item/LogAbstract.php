<?php

namespace LogReader\Item;
abstract class LogAbstract {
    
        
    /**
     *
     * @var string
     */
    protected $_timestamp;
    
    /**
     *
     * @var string 
     */
    protected $_type;
    
    /**
     *
     * @var string
     */
    protected $_message;
    
    /**
     *
     * @var boolean
     */
    protected $_isNew;
    
    /**
     * Unique id to distinct errors
     * 
     * @return string
     */
    public function getId(): string
    {
        return md5($this->getMessage());
    }

    public function getTimestamp(): string
    {
        return $this->_timestamp;
    }

    public function getType(): string
    {
        return $this->_type;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function setTimestamp($timestamp) {
        $this->_timestamp = $timestamp;
    }

    public function setType($type) {
        $this->_type = $type;
    }

    public function setMessage($message) {
        $this->_message = $message;
    }

    public function setIsNew($isNew) {
        $this->_isNew = $isNew;
    }
    
    public function populate($data) {
        foreach ($data as $prop => $value) {
            if (property_exists($this, $prop)) {
                $this->$prop = $value;
            }
        }
    }
    
}
