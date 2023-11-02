<?php

namespace LogReader\Item;

class ApachePhp extends LogAbstract {
    
    protected $_clientIp;
    
    /**
     *
     * @var array
     */
    protected $_stackTrace = array();
    
    /**
     *
     * @var string
     */
    protected $_referer = '';
    
    
    public function getReferer(): string
    {
        return $this->_referer;
    }

    public function setReferer($referer) {
        $this->_referer = $referer;
    }

    
    /**
     * 
     * @param string $line
     */
    public function appendStackTrace(string $line) {
        $this->_stackTrace[] = $line;
    }
    
    /**
     * 
     * @return array
     */
    public function getStackTrace(): array
    {
        return $this->_stackTrace;
    }
    
    /**
     * 
     * @param string $clientIp
     */
    public function setClientIp(string $clientIp) {
        $this->_clientIp = $clientIp;
    }
    
    /**
     * 
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->_clientIp;
    }

}
