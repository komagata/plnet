<?php
require_once 'DBUtils.php';

class ContentCategoryUtils
{
    function find()/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT *
            FROM content_category 
            ORDER BY id';

        $result = $db->getAll($sql);
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get($content_category_id)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT * FROM content_category WHERE id = ?';
        $result = $db->getRow($sql, array($content_category_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function array_localize(&$content_categories)
    {
        foreach ($content_categories as $key => $value)
            $content_categories[$key]['name'] = msg($value['name']);
    }
}
?>
