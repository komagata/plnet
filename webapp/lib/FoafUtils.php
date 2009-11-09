<?php
require_once 'DBUtils.php';

class FoafUtils
{
    function is_exists_by_account_and_url($account, $url)
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT f.id FROM foaf f
            JOIN member m ON f.member_id = m.id
            WHERE m.account = ?
            AND f.url = ?';

        $result = $db->getOne($sql, array($account, $url));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
            $result->toString(), E_USER_ERROR);
            return null;
        }

        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }
}
?>
