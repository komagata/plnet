<?php
/**
 * Table Definition for feed_to_entry
 */
require_once 'DB/DataObject.php';

class Feed_to_entry extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'feed_to_entry';                   // table name
    var $feed_id;                         // int(11)  not_null primary_key
    var $entry_id;                        // int(11)  not_null primary_key

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Feed_to_entry',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
