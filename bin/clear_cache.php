#!/usr/bin/env php
<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';

shell_exec('rm -f '.CACHE_DIR.'simplepie/*.spc');
shell_exec('rm -f '.SMARTY_CACHE_DIR.'/*.php');
shell_exec('rm -f '.CACHE_LITE_DIR.'/*');
?>
