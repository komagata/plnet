<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once 'FOAFParser.php';

$uri = $_SERVER['argv'][1];

$parser =& new FOAFParser();
$parser->parse($uri);
var_dump($parser->getImg());
print_r($parser->getKnowsPerson());
?>
