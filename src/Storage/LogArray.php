<?php

namespace LogReader\Storage;

use LogReader\Item\LogAbstract;

class LogArray implements LogInterface {

    protected $Config = [];
    /**
     *
     * @var array
     */
    protected $_data = array();

    public function config($config = [])
    {
        // TODO: Implement config() method.
    }


    /**
     *
     * @param array $params
     * @return array
     */
    public function load($params=[]): array
    {
        return $this->_data;
    }

    public function save(LogAbstract $item) {
        $this->_data[] = $item;
    }

    /**
     * Returns unique errors
     *
     * @param array $params
     * @return array
     */
    public function loadUnique($params=[]): array
    {
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
