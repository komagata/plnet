#!/usr/bin/env php
<?php
/*
 * entry テーブルで重複しているデータを削除するスクリプト
 */
ini_set('include_path', '/home/plnet/pear/php');

require_once dirname(dirname(__FILE__)).'/webapp/lib/Utils.php';
require_once dirname(dirname(__FILE__)).'/webapp/lib/INILoader.php';
$conf = Utils::conf('development');
$dsn = $conf['dsn'];

$db =& DB::connect($dsn);
$db->setFetchMode(DB_FETCHMODE_ASSOC);

//Select statement.
$sql_select = "SELECT id, feed_id, uri FROM entry";

//Delete statement for duplicated entry.
$sql_delete = "DELETE FROM entry WHERE feed_id = ? AND uri = ? ";

$res_select = $db->query($sql_select);
if (DB::isError($res_select)) {
  trigger_error('Failed to select from entry ' . $res_select->toString(), E_USER_ERROR);
}

$j=0;
$k=0;
while ($entry = $res_select->fetchRow()) {

  $data = array($entry['feed_id'], $entry['uri']);
  $res = $db->getOne("SELECT count(id) FROM entry WHERE feed_id = ? AND uri = ? ", $data);
  if (DB::isError($res)) {
    trigger_error('Failed to select from entry' . $res->toString(), E_USER_ERROR);
  }

  if ($res > 1) {

    $res_delete = $db->query($sql_delete, $data);
    if (DB::isError($res_delete)) {
      trigger_error('Faild to delete from entry ' . $res_delete->toString(), E_USER_ERROR);
    }
    echo "DELETE FROM entry WHERE feed_id={$entry['feed_id']} AND uri={$entry['uri']}\n";
    //echo "Delete id:{$entry['id']} fee_id:{$entry['feed_id']} uri:{$entry['uri']}\n";
    $j++;

  } else {

    //if a recored is unique create md5 uri.
    $fields_values = array('uri_md5' => md5($entry['uri']));
    $res_update = $db->autoExecute('entry', $fields_values, DB_AUTOQUERY_UPDATE, "id={$entry['id']}");
    if (DB::isError($res_update)) {
      trigger_error('Faild to update entry ' . $res_update->toString(), E_USER_ERROR);
    }
    echo "UPDATE entry SET uri_md5 = '" . md5($entry['uri']) . "' WHERE id = {$entry['id']}\n";
    //echo "Update id:{$entry['id']} fee_id:{$entry['feed_id']} uri:" . md5($entry['uri']) . "\n";
    $k++;

  }
}
echo "Total $j entry is deleted.\n";
echo "Total $k entry is updated.\n";
?>
