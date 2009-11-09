<?php
class TagUtils
{
    function get_all()/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT t.name, COUNT(t.id) AS cnt
            FROM tag t
            JOIN entry_to_tag e2t ON e2t.tag_id = t.id
            GROUP BY t.id
            ORDER BY cnt desc";
        $tags =& $db->getAll($sql);
        if (DB::isError($tags)) {
            trigger_error('TagUtils::get_all(): fetch error.'. 
                $tags->toString(), E_USER_ERROR);
            return false;
        }
        return $tags;
    }/*}}}*/

    function get_memberid_by_account($account)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT id FROM member WHERE account = ?";
        $result =& $db->getOne($sql, array($account));
        if (DB::isError($result)) {
            trigger_error('TagUtils::get_memberid_by_account(): fetch error.'. 
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;

    }/*}}}*/

    function get_tags_by_account($account)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT t.name, COUNT(t.id) AS cnt
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            JOIN entry_to_tag e2t ON e.id = e2t.entry_id
            JOIN tag t ON e2t.tag_id = t.id
            WHERE m.account = ?
            GROUP BY t.id";
        $tags = $db->getAll($sql, array($account));
        if (DB::isError($tags)) {
            trigger_error('fetch error.'. $tags->toString(), E_USER_ERROR);
            return false;
        }
        return $tags; 
    }/*}}}*/

    function get_tags_by_entry_id($entry_id)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT t.name, COUNT(t.id) AS cnt
            FROM entry e
            JOIN entry_to_tag e2t ON e.id = e2t.entry_id
            JOIN tag t ON e2t.tag_id = t.id
            WHERE e.id = ?
            GROUP BY t.id';
        $tags = $db->getAll($sql, array($entry_id));
        if (DB::isError($tags)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $tags; 
    }/*}}}*/

    function get_tagid_by_tagname($tagid)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT id FROM tag WHERE name = ?";
        $result =& $db->getOne($sql, array($tagid));
        if (DB::isError($result)) {
            trigger_error('PlnetUtils::get_tagid_from_tagname(): fetch error.'. 
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_hot_tags($limit = null)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT t.name, COUNT(t.id) AS cnt
            FROM entry e
            JOIN entry_to_tag e2t ON e.id = e2t.entry_id
            JOIN tag t ON e2t.tag_id = t.id
            WHERE ? < e.date
            GROUP BY t.id
            ORDER BY cnt desc";
        $sql .= ($limit != null) ? " LIMIT " . $limit : "";

        $tags = $db->getAll($sql, array(date("Y-m-d", time() - 172800)));
        if (DB::isError($tags)) {
            trigger_error('fetch error.'. $tags->toString(), E_USER_ERROR);
            return false;
        }
        return $tags;
    }/*}}}*/
}
?>
