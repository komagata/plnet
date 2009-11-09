#!/usr/bin/env php
<?php
require_once dirname(dirname(__FILE__)).'/webapp/lib/Utils.php';
require_once dirname(dirname(__FILE__)).'/webapp/lib/INILoader.php';
$conf = Utils::conf('production');
$pro_dsn = $conf['dsn'];
$conf = Utils::conf('development');
$dev_dsn = $conf['dsn'];

echo "$pro_dsn\n";
echo "$dev_dsn\n";

// truncate
$loader =& new INILoader(dirname(dirname(__FILE__))."/webapp/tests/fixtures/",
$dev_dsn);
$loader->truncate_all();

$pro_db =& DB::connect($pro_dsn);
$pro_db->setFetchMode(DB_FETCHMODE_ASSOC);
$dev_db =& DB::connect($dev_dsn);
$dev_db->setFetchMode(DB_FETCHMODE_ASSOC);

_copy("content_category");
_copy("content");
_copy("design");

foreach ($pro_db->getAll("SELECT * FROM member ORDER BY id") as $index => $row) {
  # member
  $res = $dev_db->autoExecute('member', $row, DB_AUTOQUERY_INSERT);
  if (DB::isError($res)) {
    trigger_error('Failed to insert member '.
    $res->toString(), E_USER_ERROR);
  } else {
    echo "member: {$index} : {$row['id']}\n";
  }

  # site
  $site_row = $pro_db->getRow('SELECT * FROM site WHERE member_id = ?', array($row['id']));
  $res = $dev_db->autoExecute('site', $site_row, DB_AUTOQUERY_INSERT);
  if (DB::isError($res)) {
    trigger_error('Failed to insert site', E_USER_ERROR);
  } else {
    echo "site : {$site_row['id']}\n";
  }
}

foreach ($pro_db->getAll("SELECT * FROM source ORDER BY id") as $index => $row) {
  # source
  #print_r($row);

  # feed
  $fields = array(
    'id'              => $row['id'],
    'uri'             => $row['uri'],
    'link'            => $row['link'],
    'title'           => $row['name'],
    'favicon'         => $row['icon'],
    'lastupdatedtime' => $row['createdtime']
  );

  # feed verify
  $cnt = $dev_db->getOne('SELECT COUNT(id) FROM feed WHERE uri = ?', array($row['uri']));
  if ($cnt == 0) {

    # insert feed
    $res = $dev_db->autoExecute('feed', $fields, DB_AUTOQUERY_INSERT);
    if ($res === false) {
      trigger_error('Failed to insert feed', E_USER_ERROR);
    } else {
      echo "source: {$index} : {$row['id']}\n";
    }
  } else {
    echo "{$row['name']} is exists\n";
  }

  # member_to_feed
  $fields = array(
    'member_id' => $row['member_id'],
    'feed_id'   => $row['id']
  );

  $res = $dev_db->autoExecute('member_to_feed', $fields, DB_AUTOQUERY_INSERT);
  if ($res === false) {
    trigger_error('Failed to insert member_to_feed', E_USER_ERROR);
  }
}

_copy("custom_template");

function _copy($table)
{
    global $pro_db, $dev_db;
    $rows = $pro_db->getAll("SELECT * FROM $table ORDER BY id");
    foreach ($rows as $index => $row) {
        $res = $dev_db->autoExecute($table, $row, DB_AUTOQUERY_INSERT);
        if (DB::isError($res)) {
            trigger_error("Failed to insert $table ".
            $res->toString(), E_USER_ERROR);
        }
    }
}
?>
