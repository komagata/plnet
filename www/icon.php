<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';

$url = $_REQUEST['url'];
if (!preg_match('/^http:\/\/.+/', $url))
  trigger_error('This URL is wrong', E_USER_ERROR);

$icon = @file_get_contents($url);

if ($icon === false or preg_match("/<html/", $icon)) {
    $icon = file_get_contents(SCRIPT_PATH.'images/page_white.gif');
}

session_cache_limiter('public');
header('Content-Type: image/x-icon');
echo $icon;
?>
