<?php
/**
 * Table Definition for foaf
 */
require_once 'DB/DataObject.php';

class Foaf extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'foaf';                            // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $member_id;                       // int(11)  not_null multiple_key
    var $url;                             // blob(196605)  not_null unique_key blob
    var $updated_on;                      // timestamp(19)  not_null unsigned zerofill binary timestamp

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Foaf',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
