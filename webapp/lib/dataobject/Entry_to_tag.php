<?php
/**
 * Table Definition for entry_to_tag
 */
require_once 'DB/DataObject.php';

class Entry_to_tag extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'entry_to_tag';                    // table name
    var $entry_id;                        // int(11)  not_null primary_key
    var $tag_id;                          // int(11)  not_null primary_key multiple_key

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Entry_to_tag',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
