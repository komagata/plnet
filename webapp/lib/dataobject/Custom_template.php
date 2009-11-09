<?php
/**
 * Table Definition for custom_template
 */
require_once 'DB/DataObject.php';

class Custom_template extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'custom_template';                 // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $member_id;                       // int(11)  not_null multiple_key
    var $template;                        // blob(196605)  not_null blob
    var $updatedtime;                     // timestamp(19)  not_null unsigned zerofill binary timestamp

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Custom_template',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
