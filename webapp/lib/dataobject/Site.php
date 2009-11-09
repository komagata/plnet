<?php
/**
 * Table Definition for site
 */
require_once 'DB/DataObject.php';

class Site extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'site';                            // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $member_id;                       // int(11)  not_null multiple_key
    var $title;                           // string(765)  not_null
    var $description;                     // blob(196605)  not_null blob
    var $updatetime;                      // timestamp(19)  not_null unsigned zerofill binary timestamp
    var $show_profile;                    // int(4)  not_null
    var $show_footprint;                  // int(4)  not_null
    var $createdtime;                     // datetime(19)  not_null binary

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Site',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getNewSite($limit = 5)
    {
        $sql = "SELECT m.account, s.title, m.createdtime FROM member m
            JOIN site s ON m.id = s.member_id
            GROUP BY m.id
            HAVING COUNT(s.id) > 0
            ORDER BY m.createdtime DESC
            LIMIT $limit";

        $this->query($sql);
        $sites = array();
        while ($this->fetch()) {
            $sites[] = $this->__clone();
        }
        return $sites;
    }
}
