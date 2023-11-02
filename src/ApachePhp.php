<?php

namespace LogReader;


class ApachePhp extends LogAbstract {

    public function read() {
        $item = new Item\ApachePhp();
        while (!$this->_file->eof()) {

            if (preg_match('/^\[(?<date>.+?)\] \[(?:.+?)\] \[client (?<client>.+?)\] (?<php_type>PHP)?(?<message>.+?)(, referer: (?<referer>.+))?$/', $this->_file->fgets(), $matches)) {
                $date = $matches['date'];
                $message = $matches['message'];
                
                if (preg_match('/^(Stack trace|[\d])/', trim($message))) {
                    //this line is part of stack trace
                    $item->appendStackTrace($message);
                } else {
                    $this->_save($item);
                    
                    $item = new Item\ApachePhp();
                    $d = explode(" ",$date);
                    if(count($d)==5){
                        $date = implode(" ",[$d[1],$d[2],$d[4],$d[3]]);
                    }
                    $timestamp = date('Y-m-d H:i:s', strtotime($date));
                    $item->setTimestamp($timestamp);
                    if (!empty($matches['php_type'])) {
                        $type = $this->_getType($message);
                    } elseif(strpos($message,'PHP Fatal error:')) {
                        $type = 'PHP Fatal error';
                        $message = str_replace('PHP Fatal error: ','',$message);
                    } elseif(strpos($message,'PHP Warning:')) {
                        $type = 'PHP Warning';
                        $message = str_replace('PHP Warning: ','',$message);
                    } else{
                        $type = 'Apache';
                    }
                    $item->setType($type);
                    if (isset($matches['referer'])) {
                        $item->setReferer($matches['referer']);
                    }
                    if (isset($matches['client'])) {
                        $item->setClientIp($matches['client']);
                    }
                    $message = str_replace(["Got error","PHP message: "],"",$message);
                    $item->setMessage($message);    
                }
            }
        }
        
        $this->_save($item);
    }

    protected function _getType($message): string
    {
        if (preg_match('/^([a-zA-Z0-9 ]+): /', $message, $matches) && isset($matches[1])) {
            return trim($matches[1]);
        }
        return 'Log';
    }
    
    protected function _save(Item\ApachePhp $item) {
        if ($item->getMessage() && $this->_storage) {
            $stackTrace = $item->getStackTrace();
            $messagesArray = array_merge(array($item->getMessage()), $stackTrace);
            $item->setMessage(implode("\n", $messagesArray));
            $this->_storage->save($item);
        }
    }

}
