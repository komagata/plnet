<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once 'INILoader.php';

if ($_SERVER['argv'][1] == '-h' or $_SERVER['argv'][1] == '--help') {
    echo <<<EOT
Usage: db_dump.php

Description:
    db_dump.php command create database dump data to ini files.

Example:
    php bin/db_dump.php

    This command create data to webapp/tests/fixtures/ directory.
    /webapp/tests/fixtures/{TABLE NAME}.ini

EOT;
    exit();
}

$conf = Utils::conf('development');
dump_ini(dirname(dirname(__FILE__))."/webapp/tests/fixtures/", $conf['dsn']);
?>
