<?php

namespace LogReader\Storage;

class LogArray implements LogInterface {
    
    /**
     *
     * @var array
     */
    protected $_data = array();


    /**
     * 
     * @return array
     */
    public function load() {
        return $this->_data;
    }

    public function save(\LogReader\Item\LogAbstract $item) {
        $this->_data[] = $item;
    }
    
    /**
     * Returns unique errors
     * 
     * @return array
     */
    public function loadUnique() {
        $uniqRows = array();
        foreach ($this->_data as $item) {
            $itemId = $item->getId();
            if (isset($uniqRows[$itemId])) {
                $newTime = strtotime($item->getTimestamp());
                $oldTime = strtotime($uniqRows[$itemId]->getTimestamp());
                if ($newTime > $oldTime) {
                    $uniqRows[$itemId] = $item;
                }
            } else {
                $uniqRows[$item->getId()] = $item;
            }
        }
        
        uasort($uniqRows, function($a, $b) {
            return strtotime($a->getTimestamp()) > strtotime($b->getTimestamp());
        });
        
        return $uniqRows;
    }

}
