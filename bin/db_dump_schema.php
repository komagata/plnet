<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';

$env = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'development';
$conf = Utils::conf($env);
dump_ini(dirname(dirname(__FILE__))."/webapp/tests/fixtures", $conf['dsn']);



#mysqldump -uplnet -pplnet strictdb -d > plnet.sql
?>
