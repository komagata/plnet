#!/usr/bin/env php
<?php
/*
 * member_to_content_category_to_feed テーブルへのデータ登録用スクリプト
 */

ini_set('include_path', '/home/plnet/pear/php');
require_once dirname(dirname(__FILE__)).'/webapp/lib/Utils.php';
require_once dirname(dirname(__FILE__)).'/webapp/lib/INILoader.php';
$conf = Utils::conf('development');
$dsn = $conf['dsn'];

$db =& DB::connect($dsn);
$db->setFetchMode(DB_FETCHMODE_ASSOC);

$sql_feed = '
  SELECT f.id, f.title, f.uri, f.link, m.id AS member_id, m.account
  FROM feed f
  JOIN member_to_feed m2f ON f.id = m2f.feed_id
  JOIN member m ON m.id = m2f.member_id'
  ;

$sql_cont = '
  SELECT cc.id, c.uri, c.format, c.content_category_id, cc.name
  FROM content c
  JOIN content_category cc ON c.content_category_id = cc.id';

$sql_truncate = 'TRUNCATE TABLE member_to_content_category_to_feed';

if (DB::isError($res = $db->query($sql_truncate))) {
    echo "Failed truncation.\n";
    trigger_error('Failed to truncate.'
    . $res->toString(), E_USER_ERROR);
}
echo "Truncated table member_to_content_category_to_feed\n";

$i=0;
foreach ($db->getAll($sql_feed) as $feed) {
  $flg = false;
  foreach ($db->getAll($sql_cont) as $cont) {
    if ($feed['uri'] != '' && compareFormat($cont['format'], $feed['uri']) === true) {
      $data = array(
          'member_id' => $feed['member_id'],
          'feed_id' => $feed['id'],
          'content_category_id ' => $cont['id']);
      $res = $db->autoExecute('member_to_content_category_to_feed', $data, DB_AUTOQUERY_INSERT);
      if (DB::isError($res)) {
        trigger_error('Failed to insert into member_to_content_category_to_feed'
        . $res->toString(), E_USER_ERROR);
      } else {
        echo " $i : {$feed['member_id']}:{$feed['account']}'s {$feed['id']}=>{$feed['link']} as {$cont['id']}=>{$cont['name']}\n".
             " INSERT INTO member_to_content_category_to_feed (member_id, conten_category_id, feed_id) VALUE ({$feed['member_id']}, {$cont['id']}, {$feed['id']})\n";
      }
      $flg = true;
    }
  }
  // Doesn't match any category.
  if ($flg === false) {
    $data = array(
        'member_id' => $feed['member_id'],
        'feed_id' => $feed['id'],
        'content_category_id ' => 8);
    $res = $db->autoExecute('member_to_content_category_to_feed', $data, DB_AUTOQUERY_INSERT);
    if (DB::isError($res)) {
      trigger_error('Failed to insert into member_to_content_category_to_feed'
      . $res->toString(), E_USER_ERROR);
    } else {
      echo " $i : {$feed['member_id']}:{$feed['account']}'s {$feed['id']}=>{$feed['link']} as {$cont['id']}=>{$cont['name']}\n".
           " INSERT INTO member_to_content_category_to_feed (member_id, conten_category_id, feed_id) VALUE ({$feed['member_id']}, {$cont['id']}, {$feed['id']})\n";
    }
  }
  $i++;
}
echo "Total $i feeds categorised. \n";

function getUserName($str_base, $str_new) {
    $parts = explode('##username##', $str_base);
    return str_replace($parts, '', $str_new);
}

function compareFormat($str_base, $str_new) {
    $username = getUserName($str_base, $str_new);
    $ary1 = @explode('##username##', $str_base);
    $ary2 = @explode($username, $str_new);
    $diff = array_diff((array)$ary1,(array) $ary2);
    if(empty($diff) === true) {
        return true;
    }
    return false;
}
?>
