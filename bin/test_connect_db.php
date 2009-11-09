<?php
require_once dirname(dirname(__FILE__)) . '/webapp/config.php';

$db =& DBUtils::connect();
$members = $db->getAll('SELECT * FROM member LIMIT 5');
print_r($members);
?>
