<?php
/**
 * Table Definition for footprint
 */
require_once 'DB/DataObject.php';

class Footprint extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'footprint';                       // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $owner_id;                        // int(11)  not_null multiple_key
    var $visitor_id;                      // int(11)  not_null multiple_key
    var $created_on;                      // timestamp(19)  not_null unsigned zerofill binary timestamp

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Footprint',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
