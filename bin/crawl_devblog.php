#!/usr/bin/env php
<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once MOJAVI_FILE;
require_once BASE_DIR . 'lib/LogUtils.php';
require_once 'Crawler.php';

set_time_limit(0);

shell_exec(BIN_DIR."clear_cache.php");

$feed1 = 'http://p0t.jp/mt/archives/plnet/index.rdf';
$feed2 = 'http://f-pig.blogspot.com/atom.xml';
$feed3 = SCRIPT_PATH.'plnet-dev/rss';

$crawler =& new Crawler();
$feeds = $crawler->getFeeds();
shell_exec('php '.BIN_DIR."crawl.php \"{$feed1}\"");
shell_exec('php '.BIN_DIR."crawl.php \"{$feed2}\"");
shell_exec('php '.BIN_DIR."crawl.php \"{$feed3}\"");
?>
