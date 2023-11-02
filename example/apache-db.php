<?php

require '../vendor/autoload.php';

$logReader = new LogReader\ApachePhp(
        "/var/log/apache2/error.log",
        (new LogReader\Storage\LogDB())->config([
            'dbhost' => '127.0.0.1:3366',
            'dbuser' => 'dbuser',
            'dbpass' => 'qwerasdf',
            'dbschema' => 'accesslogs',
            'dbtable' => 'serverlog'
        ]));
$logReader->read();

$logs = $logReader->getStorage()->load(['start'=>0,'records'=>200]);
$logs = array_reverse($logs);

$colorizeType = function($type) {
    switch ($type) {
        case 'Warning':
            $color = '#aa0';
            break;
        case 'Fatal error':
        case 'Catchable fatal error':
            $color = '#a00';
            break;
        default:
            $color = '#000';
    }
    return '<span style="color: '.$color.'">' . $type . '</span>';
};
if (count($logs)) {
    echo '
    <table >
        <tr>
            <th>Date</th>
            <th>Error type</th>
            <th>Message</th>
            <th>Referrer</th>
        </tr>';
    foreach ($logs as $item) {
        echo '
        <tr>
            <td>' . $item->getTimestamp() . '</td>
            <td>' . colorizeType($item->getType()) . '</td>
            <td>' . str_replace(["\n",'\n','\\n'],'<br />',htmlspecialchars($item->getMessage())) . '</td>
            <td>' . htmlspecialchars($item->getReferer()) . '</td>
        </tr>';
    }
    echo '
    </table>';
} else {
    echo '<p>
        <em>Empty data.</em>
    </p>';
}
