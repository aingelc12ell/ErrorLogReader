<?php

require '../vendor/autoload.php';

$logReader = new LogReader\ApachePhp(
        "/var/log/apache2/error.log",
        new LogReader\Storage\LogArray());
$logReader->read();

$logs = $logReader->getStorage()->load();
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
?>

<p>File '<?=$logReader->getFilename()?>':</p>

<?php if (count($logs)) { ?>
    <table >
        <tr>
            <th>Date</th>
            <th>Error type</th>
            <th>Message</th>
            <th>Referrer</th>
        </tr>
        <?php foreach ($logs as $item) { ?>
        <tr>
            <td><?=$item->getTimestamp()?></td>
            <td><?=$colorizeType($item->getType())?></td>
            <td><?=nl2br(htmlspecialchars($item->getMessage()))?></td>
            <td><a href="<?=htmlspecialchars($item->getReferer())?>"><?=htmlspecialchars($item->getReferer())?></a></td>
        </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <p>
        <em>Empty data.</em>
    </p>
<?php } ?>
