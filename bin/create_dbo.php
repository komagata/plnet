<?php
require_once dirname(dirname(__FILE__)) . '/webapp/config.php';
require_once 'DB/DataObject/Generator.php';

$generator =& new DB_DataObject_Generator();
$generator->start();
?>
