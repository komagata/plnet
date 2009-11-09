<?php
require_once 'PHP/Compat/Function/file_put_contents.php';
require_once dirname(__FILE__) . '/DBUtils.php';

class PlnetUtils
{
    function get_memberid_by_account($account)
    {
        $db =& DBUtils::connect();
        $sql = "SELECT id FROM member WHERE account = ?";
        $result =& $db->getOne($sql, array($account));
        if (DB::isError($result)) {
            trigger_error('PlnetUtils::get_memberid_by_account(): fetch error.'. 
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;

    }

    function get_tags_by_account($account)
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = "SELECT t.name, COUNT(t.id) AS cnt
            FROM member m
            JOIN source s ON m.id = s.member_id
            JOIN source_to_feed s2f ON s.id = s2f.source_id
            JOIN feed f ON s2f.feed_id = f.id
            JOIN feed_to_entry f2e ON f.id = f2e.feed_id
            JOIN entry e ON f2e.entry_id = e.id
            JOIN entry_to_tag e2t ON e.id = e2t.entry_id
            JOIN tag t ON e2t.tag_id = t.id
            WHERE m.id = ?
            GROUP BY t.id";
        $tags = $db->getAll($sql, array($member_id));
        if (DB::isError($tags)) {
            trigger_error('fetch error.'. $tags->toString(), E_USER_ERROR);
            return false;
        }
        return $tags; 
    }

    function get_tagid_by_tagname($tagid)
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
    }
}
?>
