<?php
ini_set('include_path', '.:/home/attach/pear/php');

require_once 'Date.php';

$date = '2006-08-07T16:33:38+0900';
//$date = '2006-08-07T16:33:38';
$d =& new Date();
$d->setDate($date);
echo $d->getDate(DATE_FORMAT_UNIXTIME)."\n";
echo $d->getDate(DATE_FORMAT_ISO)."\n";

$reg = '/([0-9]{2,4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2})(:([0-9]{2}))?(\.([0-9]{2}))?(\+|-)([0-9]{2})([0-9]{2})/i';
if (preg_match($reg, $date, $matches)) {
    var_dump($matches);
}
?>
