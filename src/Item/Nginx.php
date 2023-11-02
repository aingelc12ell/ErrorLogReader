<?php

namespace LogReader\Item;

class Nginx extends LogAbstract {
    
    /**
     *
     * @var string
     */
    protected $_referrer = '';
    
    /**
     *
     * @var string
     */
    protected $_request = '';

    /**
     *
     * @var string
     */
    protected $_host = '';
    
    
    public function getRequest(): string
    {
        return $this->_request;
    }

    public function getHost(): string
    {
        return $this->_host;
    }

    public function setRequest($request) {
        $this->_request = $request;
    }

    public function setHost($host) {
        $this->_host = $host;
    }

    
    public function getReferrer(): string
    {
        return $this->_referrer;
    }

    public function setReferrer($referer) {
        $this->_referrer = $referer;
    }

    public function getRequestUrl(): string
    {
        $request = preg_replace('/^GET (.+) HTTP.+/', '$1', $this->getRequest());
        return 'http://' . $this->getHost() . $request;
    }

}
