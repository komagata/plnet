<?php
/**
 * Table Definition for tag
 */
require_once 'DB/DataObject.php';

class Tag extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'tag';                             // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $name;                            // string(765)  not_null unique_key
    var $updatedtime;                     // timestamp(19)  not_null multiple_key unsigned zerofill binary timestamp

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Tag',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
