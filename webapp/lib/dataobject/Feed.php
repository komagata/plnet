<?php
/**
 * Table Definition for feed
 */
require_once 'DB/DataObject.php';

class Feed extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'feed';                            // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $uri;                             // string(765)  not_null unique_key
    var $link;                            // string(765)  not_null
    var $title;                           // string(765)  not_null
    var $description;                     // blob(196605)  blob
    var $favicon;                         // string(765)  not_null
    var $lastupdatedtime;                 // datetime(19)  not_null multiple_key binary

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Feed',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getListsByAccount($account)
    {
        $sql = 'SELECT f.id, f.uri, f.link, f.title, f.description,
            f.favicon, f.lastupdatedtime, COUNT(e.id) AS cnt
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            LEFT JOIN entry e ON f.id = e.feed_id
            WHERE m.account = \''.$this->escape($account).'\''.'
            GROUP BY f.id
            ORDER BY f.id';

        $this->query($sql);
        $lists = array();
        while ($this->fetch()) {
            $lists[] = $this->__clone();
        }
        return $lists;
    }
}
