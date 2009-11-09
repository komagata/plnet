<?php
require_once 'DBUtils.php';

class SiteUtils
{
    function get_by_account($account)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT s.id, s.title, s.description
            FROM member m
            JOIN site s ON m.id = s.member_id
            WHERE m.account = ?';
        $result = $db->getRow($sql, array($account));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/
}
?>
