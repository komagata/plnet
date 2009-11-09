<?php
error_reporting(E_ALL);
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once 'simplepie.inc';

$feed = new SimplePie();
$feed->feed_url($_SERVER['argv'][1]);
$feed->enable_caching(false);
$res = $feed->init();
echo "res: $res\n";
print_r($feed);

foreach ($feed->get_items() as $item) {
  print_r($item);
  echo "Date: ".$item->get_date()."\n";
}
?>
