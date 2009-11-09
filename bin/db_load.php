<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once 'INILoader.php';

if ($_SERVER['argv'][1] == '-h' or $_SERVER['argv'][1] == '--help') {
    echo <<<EOT
Usage: db_load.php

Description:
    db_load.php command load data into database from ini files.

Example:
    php bin/db_load.php

    This command read data from webapp/tests/fixtures/ directory.
    /webapp/tests/fixtures/{TABLE NAME}.ini

EOT;
    exit();
}

$conf = Utils::conf('development');
load_ini(dirname(dirname(__FILE__))."/webapp/tests/fixtures/", $conf['dsn']);
?>
