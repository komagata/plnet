<?php
define('RSS_MAX', 16);

require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once 'FeedWriter.php';
require_once 'FOAFWriter.php';
require_once 'phpMixi.class.php';

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$target = isset($_REQUEST['target']) ? $_REQUEST['target'] : 'diary';

$mixi =& new PHP_Mixi('komagata@p0t.jp', 'komagata', true);
$mixi->cache_dir = MIXI_CACHE_DIR;
$mixi->login();

switch ($target) {
case 'profile' :
    $profile = $mixi->parse_show_friend($id);
    $profile = array_e2u($profile);
print_r($profile);

    $writer =& new FOAFWriter();
    $writer->setProfile(array(
        'nick' => 'komagata',
        'bio' => 'baiooo',
        'mbox' => 'mailfoo',
        'img' => 'http://foobar.com/foo.img',
        'weblog' => 'http://p0t.jp'
    ));
    $writer->addInterest(array(
        'title' => 'net',
        'uri'   => 'http://foobar.com/net'
    ));
    $writer->addInterest(array(
        'title' => 'bike',
        'uri'   => 'http://foobar.com/bike'
    ));
    $writer->addKnow(array(
        'nick' => 'kawadu',
        'mbox'   => 'aaa',
        'weblog' => 'http://kawadu.foobar.com'
    ));
    $writer->addKnow(array(
        'nick' => 'mikami',
        'mbox'   => 'mmm',
        'weblog' => 'http://mikami.foobar.com'
    ));
    $writer->addKnow(array(
        'nick' => 'sasama',
        'mbox'   => 'gggg',
        'weblog' => 'http://sasama.foobar.com'
    ));

    $writer->display();

    break;
case 'diary' :
default : 
    $channel = array();
    $items = array();
    $profile = $mixi->parse_show_friend($id);
    $profile = array_e2u($profile);
    $author = preg_replace("/さん$/", '', $profile['name']);

    $channel['title'] = $profile['name'].'の日記';
    $channel['description'] = $profile['name'].'のmixiの日記';
    $channel['link'] = "http://mixi.jp/list_diary.pl?id={$id}";
    $channel['uri'] = SCRIPT_PATH."mixi.php?id={$id}";
    $channel['author'] = $author;

    $diaries = $mixi->parse_list_diary($id);
    if (count($diaries) == 0) {
        trigger_error('Failed to get diary', E_USER_ERROR);
    }

    $writer =& new FeedWriter();
    $writer->setChannel($channel);

    for ($i = 0; $i < RSS_MAX; $i++) {
        $diary = $diaries[$i];

        preg_match('/view_diary\.pl\?id=(\d+)&owner_id=(\d+)/is', 
            $diary['link'], $match);
        $article = $mixi->parse_view_diary($match[1], $match[2]);

        $writer->addItem(array(
            'title'       => e2u($article['subject']),
            'link'        => $diary['link'],
            'description' => e2u($article['content']),
            'author'      => $author,
            'date'        => $article['date']
        ));
    }

    $writer->display();
}

function array_e2u($array)
{
    $result = array();
    foreach ($array as $key => $value) {
        $result[e2u($key)] = e2u($value);
    }
    return $result;
}

function e2u($str)
{
    return mb_convert_encoding($str, 'UTF-8', 'EUC-JP');
}
?>
