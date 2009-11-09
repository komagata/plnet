<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
echo file_get_contents($_SERVER['argv'][1]);
?>
