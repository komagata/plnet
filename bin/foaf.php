<?php
define('RDFAPI_INCLUDE_DIR', dirname(dirname(__FILE__)).'/lib/rdfapi-php/api/');
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once RDFAPI_INCLUDE_DIR.'RdfAPI.php';
require_once RDFAPI_INCLUDE_DIR.'syntax/RdfParser.php';

$uri = $_SERVER['argv'][1];

$model = ModelFactory::getDefaultModel();
$model->load($uri);
print_r($model);
?>
