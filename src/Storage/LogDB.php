<?php

namespace LogReader\Storage;
use LogReader\Item\ApachePhp;
use LogReader\Item\LogAbstract;

class LogDB implements LogInterface {

    const DBSTRUCT = "
        `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `Serial` varchar(40) NOT NULL,
        `Date` datetime DEFAULT NULL,
        `Type` varchar(40) DEFAULT NULL,
        `Error` text DEFAULT NULL,
        `Referrer` text DEFAULT NULL,
        PRIMARY KEY (`ID`),
        KEY `Serial` (`Serial`)
    ";
    protected $Config = [
        'dbhost' => '127.0.0.1:3306',
        'dbuser' => 'root',
        'dbpass' => '',
        'dbschema' => 'errorlog',
        'dbtable' => 'logtable',
        'records' => 20
    ];
    protected $DB = null;

    /**
     *
     * @var array
     */
    protected $_data = array();


    /**
     *
     * @param array $params
     * @return array
     */
    public function load($params=[]): array
    {
        # return $this->_data;
        # read from DB
        $this->DBConnect();
        $start = intval($params['start'] ?? 0);
        $start = max($start, 0);
        $records = intval($this->Config['records'] ?? 20);
        $records = $records < 0 ? 20 : $records;
        $res = $this->DB->query("SELECT * from `" . $this->Config['dbtable'] ."`"
            ." ORDER BY `Date` DESC"
            ." LIMIT " . $start . "," . $records);
        while($r = $res->fetch_assoc()){
            $item = new ApachePhp();
            $item->setTimestamp($r['Date']);
            $item->setType($r['Type']);
            $item->setReferer($r['Referrer']);
            $item->setMessage($r['Error']);
            $this->_data[] = $item;
        }
        return $this->_data;
    }

    public function save(LogAbstract $item) {
        # $this->_data[] = $item;
        # send to DB
        $this->DBConnect();

        $uniqserial = sha1($item->getTimestamp().$item->getMessage());
        if(!$this->isSaved($uniqserial)) {
            $this->DB->query("INSERT INTO `" . $this->Config['dbtable'] . "`"
                . "(`Serial`,`Date`,`Type`,`Error`,`Referrer`)"
                . " VALUES("
                . "'" . $uniqserial ."'"
                . ",'" . $item->getTimestamp() . "'"
                . ",'" . $this->DB->real_escape_string($item->getType()) . "'"
                . ",'" . $this->DB->real_escape_string($item->getMessage()) . "'"
                . ",'" . $this->DB->real_escape_string($item->getReferer()) . "'"
                . ")"
            );
        }
    }
    private function isSaved($serial): bool
    {
        $this->DBConnect();
        $res = $this->DB->query("SELECT * from `" . $this->Config['dbtable'] ."`"
            ." where `Serial`='" . preg_replace('/[^A-Fa-f0-9]/','',$serial) . "'");
        return is_a($res,'mysqli_result') && $res->num_rows > 0;
    }
    public function config($config=[]): LogDB
    {
        if(is_array($config)){
            foreach($config as $k => $v){
                if(isset($this->Config[$k])){
                    $this->Config[$k] = $v;
                }
            }
        }
        return $this;
    }
    private function DBConnect(){
        if(is_null($this->DB)){
            $this->DB = new \mysqli($this->Config['dbhost'], $this->Config['dbuser'], $this->Config['dbpass'], $this->Config['dbschema']);
        }
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
