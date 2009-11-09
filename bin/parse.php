<?php
require_once dirname(dirname(__FILE__)) . '/webapp/config.php';
require_once 'FeedParser.php';

$uri = $_SERVER['argv'][1];

$feed = new FeedParser($uri);
$res = $feed->parse();
print_r($feed->toArray());
?>
