<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';

define('RSS_MAX', 16);
if (!defined('MIXI_CACHE_DIR')) define('MIXI_CACHE_DIR', '/tmp/');

require_once 'FOAFWriter.php';
require_once 'phpMixi.class.php';

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

$mixi =& new PHP_Mixi('komagata@p0t.jp', 'komagata', true);
$mixi->cache_dir = MIXI_CACHE_DIR;
$mixi->login();

$profile = $mixi->parse_show_friend($id);
$profile = array_e2u($profile);

//print_r($profile);

$friends = $mixi->get_all_friend($id);
$friends = array_e2u($friends);

//print_r($friends);

$blog_home = 'http://mixi.jp/show_friend.pl?id=';
$tag_uri = SCRIPT_PATH.'tag/';
$mixi_foaf = SCRIPT_PATH.'?id=';

$writer =& new FOAFWriter();
$writer->setProfile(array(
    'nick' => $profile['name'],
    'bio' => $profile['description'],
    'img' => $profile['image'],
    'weblog' => "$blog_home{$id}"
));

$hobbies = split(", ", $profile['interests']);
foreach ($hobbies as $hobby) {
    $writer->addInterest(array(
        'title' => $hobby,
        'uri'   => "$tag_uri{$hobby}/"
    ));
}

foreach ($friends as $friend) {
    $writer->addKnow(array(
        'nick' => $friend['name'],
        'img' => $friend['logo'],
        'seeAlso' => SCRIPT_PATH."?id={$friend['id']}",
        'weblog' => $friend['link']
    ));
}

$writer->display();

function array_e2u($array)
{
    $result = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result[e2u($key)] = array_e2u($value);
        } else {
            $result[e2u($key)] = e2u($value);
        }
    }
    return $result;
}

function e2u($str)
{
    return mb_convert_encoding($str, 'UTF-8', 'EUC-JP');
}
?>
