<?php
require_once 'DBUtils.php';

class ContentUtils
{
    function get_list($limit)
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT c.id, c.name, cc.id AS category_id, cc.name AS category_name, 
            c.uri, c.icon, c.format, c.target, c.foaf_format, c.description
            FROM content c
            JOIN content_category cc ON c.content_category_id = cc.id
            ORDER BY cc.id, c.id
            LIMIT ?';

        $result = $db->getAll($sql, array($limit));
        if (DB::isError($result)) {
            trigger_error('ContentUtils::get_list(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }
}
?>
