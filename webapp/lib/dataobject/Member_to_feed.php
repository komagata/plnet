<?php
/**
 * Table Definition for member_to_feed
 */
require_once 'DB/DataObject.php';

class Member_to_feed extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'member_to_feed';                  // table name
    var $member_id;                       // int(11)  not_null primary_key
    var $feed_id;                         // int(11)  not_null primary_key multiple_key

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Member_to_feed',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function feedsIsExistsByMemberId($memberId)
    {
        $this->member_id = $memberId;
        if ($this->count() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
