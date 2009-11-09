<?php
/**
 * Table Definition for feedback
 */
require_once 'DB/DataObject.php';

class Feedback extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'feedback';                        // table name
    var $id;                              // int(11)  not_null primary_key
    var $name;                            // string(765)  not_null
    var $comment;                         // blob(196605)  not_null blob
    var $created_at;                      // timestamp(19)  not_null unsigned zerofill binary timestamp

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Feedback',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function getList($limit = null, $offset = 0, $orderby = null)
    {
        $items = array();
        if (!is_null($limit)) $this->limit($limit, $offset);
        if (!is_null($orderby) && is_array($orderby)) {
            foreach ($orderby as $column => $sort) {
                $this->orderBy("$column $sort");
            }
        }
        $this->find();
        while($this->fetch()) $items[] = $this;
        return $items;
    }
}
