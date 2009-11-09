<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once MOJAVI_FILE;
require_once BASE_DIR . 'lib/LogUtils.php';
require_once LIB_DIR . 'Crawler.php';

set_time_limit(0);

$crawler =& new Crawler();
$feeds = $crawler->getFeeds();
$cnt = count($feeds);
LogUtils::debug('[Crawling start]');
$success = 0;
foreach ($feeds as $feed) {
    $success++;
    shell_exec('php '.BIN_DIR."crawl.php \"{$feed}\"");
    LogUtils::debug("[Crawling: {$success}/{$cnt}]");
    LogUtils::debug("Memory usage: ".
    number_format(memory_get_usage()));
    sleep(0.1);
}
LogUtils::debug("[Crawling finished: {$success}/{$cnt}]");
?>
