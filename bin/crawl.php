#!/usr/bin/env php
<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once MOJAVI_FILE;
require_once BASE_DIR . 'lib/LogUtils.php';
require_once LIB_DIR . 'Crawler.php';

set_time_limit(30);

$uri = $_SERVER['argv'][1];

$crawler =& new Crawler();
if ($crawler->crawl($uri) === false) {
    trigger_error("Failed to crawl feed: $uri", E_USER_NOTICE);
}
?>
