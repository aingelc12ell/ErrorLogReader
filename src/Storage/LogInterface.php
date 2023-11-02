<?php

namespace LogReader\Storage;

interface LogInterface {
    
    public function save(\LogReader\Item\LogAbstract $item);
    
    public function load();
    
    public function loadUnique();
}