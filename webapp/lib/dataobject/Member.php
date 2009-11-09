<?php
/**
 * Table Definition for member
 */
require_once 'DB/DataObject.php';

class Member extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'member';                          // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $design_id;                       // int(11)  not_null multiple_key
    var $account;                         // string(96)  not_null unique_key
    var $password;                        // string(120)  not_null
    var $email;                           // string(765)  not_null
    var $language;                        // string(765)  
    var $firstname;                       // string(765)  
    var $familyname;                      // string(765)  
    var $nickname;                        // string(765)  
    var $photo;                           // blob(65535)  blob binary
    var $gender;                          // int(11)  
    var $homepage;                        // string(765)  
    var $birthdate;                       // date(10)  binary
    var $aim;                             // string(765)  
    var $yahoo;                           // string(765)  
    var $skype;                           // string(765)  
    var $msn;                             // string(765)  
    var $googletalk;                      // string(765)  
    var $self_introduction;               // blob(196605)  blob
    var $createdtime;                     // datetime(19)  not_null binary

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Member',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
