<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once MOJAVI_FILE;
require_once 'unit_tester.php';
require_once 'reporter.php';

$test =& new GroupTest('All Test');

$unit_dir = dirname(dirname(__FILE__)).'/webapp/tests/unit/';
$d = dir($unit_dir);
while ($entry = $d->read()) {
    if (preg_match("/^Test.+\.php$/", $entry)) {
        $test->addTestFile($entry);
    }
}
$d->close();

$test->run(new TextReporter());
?>
