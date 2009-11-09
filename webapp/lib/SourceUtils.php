<?php
require_once LIB_DIR . 'DBUtils.php';

class SourceUtils
{
    function get_source_by_id($source_id)
    {
        $db =& DBUtils::connect();
        $sql = "SELECT s.name, s.link 
            FROM source s 
            WHERE s.id = ?"; 

        $result = $db->getRow($sql, array($source_id));
        if (DB::isError($result)) {
            trigger_error('SourceUtils::get_source_by_id(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result; 
    }

    function get_sources_by_account($account)
    {
        $db =& DBUtils::connect();
        $sql = "SELECT s.id, s.name, s.uri, s.link, s.icon 
            FROM member m 
            JOIN source s ON m.id = s.member_id 
            WHERE m.account = ? 
            ORDER BY s.id";

        $result = $db->getAll($sql, array($account));
        if (DB::isError($result)) {
            trigger_error('PlnetUtils::get_sources_by_account(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result; 
    }
}
?>
