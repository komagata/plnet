<?php
/**
 * Table Definition for member_temporary
 */
require_once 'DB/DataObject.php';

class Member_temporary extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'member_temporary';                // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $design_id;                       // int(11)  not_null
    var $account;                         // string(96)  not_null
    var $password;                        // string(120)  not_null
    var $email;                           // string(765)  not_null
    var $createdtime;                     // datetime(19)  not_null binary
    var $activate_key;                    // string(96)  not_null

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Member_temporary',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
