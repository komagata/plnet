<?php
require_once 'PHP/Compat/Function/file_get_contents.php';

echo @file_get_contents($_GET['uri']);
?>
