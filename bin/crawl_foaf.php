<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
define('RDFAPI_INCLUDE_DIR', BASE_LIB_DIR.'rdfapi-php/api/');
require_once RDFAPI_INCLUDE_DIR.'RdfAPI.php';
require_once RDFAPI_INCLUDE_DIR.'syntax/RdfParser.php';

$uri = $_SERVER['argv'][1];

$model = ModelFactory::getDefaultModel();
$model->load($uri);
print_r($model);
?>
