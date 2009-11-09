<?php
require_once 'DBUtils.php';

class MemberUtils
{
    function total()/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT COUNT(id) AS cnt FROM member";

        $result = $db->getOne($sql);
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_id_by_account($account)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT id FROM member WHERE account = ? LIMIT 1";
        $result = $db->getOne($sql, array($account));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_by_id($id)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT * FROM member WHERE id = ? LIMIT 1';
        $result = $db->getRow($sql, array($id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_by_account($account)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT * FROM member WHERE account = ? LIMIT 1';
        $result = $db->getRow($sql, array($account));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_list_by_content_category_name($name)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT m.id, m.account, m.photo, m.self_introduction, s.show_profile
                FROM member m
                JOIN site s ON s.member_id = m.id
                JOIN member_to_content_category_to_feed m2cc2f ON m2cc2f.member_id = m.id
                JOIN feed f ON m2cc2f.feed_id = f.id
                JOIN content_category cc ON m2cc2f.content_category_id = cc.id
                WHERE cc.name = ?
                GROUP BY m.id
                ORDER BY m.createdtime DESC
                ';

        $result = $db->getAll($sql, array($name));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_list_by_tag_name($name, $limit = null)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT m.id, m.account, m.photo, count(t.id) AS cnt
                FROM member m
                JOIN member_to_feed m2f ON m.id = m2f.member_id
                JOIN feed f ON f.id = m2f.feed_id
                JOIN entry e ON f.id = e.feed_id
                JOIN entry_to_tag e2t ON e.id = e2t.entry_id
                JOIN tag t ON t.id = e2t.tag_id
                JOIN site s ON s.member_id = m.id
                WHERE t.name = ?
                GROUP BY m.id
                ORDER BY cnt DESC';
        if ($limit) $sql .= " LIMIT $limit";

        $result = $db->getAll($sql, array($name));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/
}
?>
