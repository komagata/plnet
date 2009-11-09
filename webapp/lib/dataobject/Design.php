<?php
/**
 * Table Definition for design
 */
require_once 'DB/DataObject.php';

class Design extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'design';                          // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $name;                            // string(765)  not_null
    var $file;                            // string(765)  not_null
    var $thumbnail;                       // string(765)  not_null
    var $author;                          // string(765)  not_null
    var $updatetime;                      // timestamp(19)  not_null unsigned zerofill binary timestamp

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Design',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
