<?php
/**
 * Table Definition for entry
 */
require_once 'DB/DataObject.php';

class Entry extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'entry';                           // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $feed_id;                         // int(11)  not_null multiple_key
    var $uri;                             // blob(196605)  not_null blob
    var $uri_md5;                         // string(96)  not_null
    var $title;                           // string(765)  not_null
    var $description;                     // blob(196605)  not_null blob
    var $author;                          // string(765)  not_null
    var $date;                            // datetime(19)  not_null multiple_key binary
    var $lastupdatedtime;                 // datetime(19)  not_null binary

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Entry',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function entryIsExistsByMemberId($memberId)
    {
        $sql = 'SELECT COUNT(e.id) AS cnt 
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            LEFT JOIN entry e ON f.id = e.feed_id
            WHERE m.id = \''.$this->escape($memberId).'\'';

        $this->query($sql);
        $this->fetch();
        $res = $this->__clone();
        if ($res->cnt > 0) {
            return true;
        } else {
            return false;
        }
    }
}
