<?php

namespace LogReader\Storage;

use LogReader\Item\LogAbstract;

interface LogInterface {

    public function config($config=[]);
    
    public function save(LogAbstract $item);
    
    public function load($params=[]);
    
    public function loadUnique($params=[]);
}