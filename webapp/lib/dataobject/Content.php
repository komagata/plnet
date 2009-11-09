<?php
/**
 * Table Definition for content
 */
require_once 'DB/DataObject.php';

class Content extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    var $__table = 'content';                         // table name
    var $id;                              // int(11)  not_null primary_key auto_increment
    var $content_category_id;             // int(11)  not_null multiple_key
    var $name;                            // string(765)  not_null
    var $uri;                             // blob(196605)  not_null blob
    var $icon;                            // string(765)  not_null
    var $format;                          // blob(196605)  not_null blob
    var $foaf_format;                     // blob(196605)  not_null blob
    var $description;                     // blob(196605)  not_null blob
    var $target;                          // string(765)  not_null
    var $updatetime;                      // timestamp(19)  not_null unsigned zerofill binary timestamp

    /* ZE2 compatibility trick*/
    function __clone() { return $this;}

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Content',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
