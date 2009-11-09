<?php
require_once 'DBUtils.php';
require_once 'MemberUtils.php';

class EntryUtils
{
    function get_archives_by_account($account)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT YEAR(e.date) AS y, DATE_FORMAT(e.date, '%m') AS m
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE m.account = ?
            GROUP BY CONCAT(YEAR(e.date), MONTH(e.date))
            ORDER BY e.date DESC";

        $result = $db->getAll($sql, array($account));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }

        return $result;
    }/*}}}*/

    function get_by_account_and_id($account, $entry_id)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT e.id, e.uri, e.title, e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date,
             f.title AS feed_title, f.link AS feed_link, f.uri AS feed_uri, f.favicon
             FROM member m
             JOIN member_to_feed m2f ON m.id = m2f.member_id
             JOIN feed f ON m2f.feed_id = f.id
             JOIN entry e ON f.id = e.feed_id
             WHERE m.account = ?
             AND e.id = ?';

        $result = $db->getRow($sql, array($account, $entry_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_last_update($account)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT UNIX_TIMESTAMP(e.lastupdatedtime) AS lastupdatedtime 
            FROM member m 
            JOIN member_to_feed m2f ON m.id = m2f.member_id 
            JOIN entry e ON m2f.feed_id = e.feed_id 
            WHERE m.account = ? 
            ORDER BY e.lastupdatedtime DESC 
            LIMIT 1";

        $result = $db->getOne($sql, array($account));
        if (DB::isError($result)) {
            trigger_error('EntryUtils::get_by_account_and_id(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_last_update_by_account_feed_id($account, $feed_id)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT e.id, e.uri, e.title, e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE m.account = ?
            AND f.id = ?
            ORDER BY e.date DESC
            LIMIT 1';

        $result = $db->getOne($sql, array($account, $feed_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_by_account_feed_id($account, $feed_id, $limit = null, $offset = 0)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = 'SELECT e.id, e.uri, e.title, e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE f.id = ?
            AND m2f.member_id = ?
            ORDER BY e.date DESC';
        if (!is_null($limit)) $sql .= " LIMIT $offset, $limit";
        $result = $db->getAll($sql, array($feed_id, $member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_count_by_account_feed_id($account, $feed_id)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = 'SELECT COUNT(e.id) AS CNT
            FROM member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE f.id = ?
            AND m2f.member_id = ?';
        $result = $db->getOne($sql, array($feed_id, $member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function find_by_member_id_and_category_id($member_id, $category_id, $limit = null, $offset = 0)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT e.id, e.uri, e.title, e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM member_to_content_category_to_feed m2cc2f
            JOIN feed f ON m2cc2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE m2cc2f.content_category_id = ?
            AND m2cc2f.member_id = ?
            ORDER BY e.date DESC';
        if (!is_null($limit)) $sql .= " LIMIT $offset, $limit";
        $result = $db->getAll($sql, array($category_id, $member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function find_count_by_member_id_and_category_id($member_id, $category_id)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT COUNT(e.id) AS CNT
            FROM member_to_content_category_to_feed m2cc2f
            JOIN feed f ON m2cc2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE m2cc2f.content_category_id = ?
            AND m2cc2f.member_id = ?';
        $result = $db->getOne($sql, array($category_id, $member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_by_account($account, $limit = null, $offset = 0)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = 'SELECT e.id, e.uri, e.title, e.description, e.author, 
            UNIX_TIMESTAMP(e.date) AS date,
            UNIX_TIMESTAMP(e.lastupdatedtime) AS lastupdatedtime, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM  member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE m2f.member_id = ?
            ORDER BY e.date DESC';
        if (!is_null($limit)) $sql .= " LIMIT $offset, $limit";
        $result = $db->getAll($sql, array($member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_count_by_account($account)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = 'SELECT COUNT(e.id) AS CNT
            FROM  member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE m2f.member_id = ?';
        $result = $db->getOne($sql, array($member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_last_update_by_account($account)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT UNIX_TIMESTAMP(e.lastupdatedtime) AS lastupdatedtime 
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN entry e ON m2f.feed_id = e.feed_id
            WHERE m.account = ?
            ORDER BY e.lastupdatedtime DESC
            LIMIT 1";

        $result = $db->getOne($sql, array($account));
        if (DB::isError($result)) {
            trigger_error('EntryUtils::get_last_update_by_account(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_by_account_and_query($account, $query, $limit = null, $offset = 0)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql ="SELECT e.id, e.uri, e.title, e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date,
            f.title AS feed_title, f.link AS feed_link,
            f.uri AS feed_uri, f.favicon
            FROM member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE (e.title LIKE ?
            OR e.description LIKE ?)
            AND m2f.member_id = ?
            ORDER BY e.date DESC";
        if (!is_null($limit)) $sql .= " LIMIT $offset, $limit";
        $result = $db->getAll($sql, array("%$query%", "%$query%", $member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_count_by_account_and_query($account, $query)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql ="SELECT COUNT(e.id) AS CNT
            FROM member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE (e.title LIKE ?
            OR e.description LIKE ?)
            AND m2f.member_id = ?";
        $result = $db->getOne($sql, array("%$query%", "%$query%", $member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_last_update_by_account_and_year_month($account, $year, $month)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT e.id, e.uri, e.title, e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE DATE_FORMAT(e.date, '%Y%m') = ?
            AND m.account = ?
            ORDER BY e.date DESC
            LIMIT 1";

        $result =& $db->getOne($sql, array($year.$month, $account));
        if (DB::isError($result)) {
            trigger_error('EntryUtils::'.
            'get_last_update_by_account_and_year_month(): fetch error.'.
            $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_by_account_and_year_month($account, $year, $month, $limit = null, $offset = 0)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = "SELECT e.id, e.uri, e.title, e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM  member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE DATE_FORMAT(e.date, '%Y%m') = ?
            AND m2f.member_id = ?
            ORDER BY e.date DESC";
        if (!is_null($limit)) $sql .= " LIMIT $offset, $limit";
        $result =& $db->getAll($sql, array($year.$month, $member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_count_by_account_and_year_month($account, $year, $month)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = "SELECT COUNT(e.id) AS CNT
            FROM  member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE DATE_FORMAT(e.date, '%Y%m') = ?
            AND m2f.member_id = ?";
        $result =& $db->getOne($sql, array($year.$month, $member_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_last_update_by_account_and_tagname($account, $tagname)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = "SELECT UNIX_TIMESTAMP(e.lastupdatedtime) AS lastupdatedtime
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN entry e ON m2f.feed_id = e.feed_id
            JOIN entry_to_tag e2t ON e.id = e2t.entry_id
            JOIN tag t ON e2t.tag_id = t.id
            WHERE t.name = ?
            AND m.account = ?
            ORDER BY e.lastupdatedtime DESC
            LIMIT 1";

        $result =& $db->getOne($sql, array($tagname, $account));
        if (DB::isError($result)) {
            trigger_error('EntryUtils::get_last_update_by_account_and_tagname(): fetch error.'.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_by_tagname($tagname, $limit = null)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT m.account, e.id, e.uri, e.title, 
            e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            JOIN entry_to_tag e2t ON e.id = e2t.entry_id
            JOIN tag t ON e2t.tag_id = t.id
            WHERE t.name = ?
            ORDER BY e.date DESC';

        if ($limit) $sql .= " LIMIT $limit";

        $result =& $db->getAll($sql, array($tagname));
        if (DB::isError($result)) {
            trigger_error('EntryUtils::get_entries_by_tagname(): fetch error.'.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_by_account_and_feed_id($account, $feed_id)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT m.account, e.id, e.uri, e.title, 
            e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            WHERE m.account = ?
            AND f.id = ?
            ORDER BY e.date DESC';

        $result =& $db->getAll($sql, array($account, $feed_id));
        if (DB::isError($result)) {
            trigger_error('EntryUtils::get_entries_by_feed_id(): fetch error.'.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_by_account_and_tagname($account, $tagname, $limit = null, $offset = 0)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = "SELECT e.id, e.uri, e.title, e.description, e.author,
            UNIX_TIMESTAMP(e.date) AS date, 
            f.title AS feed_title, f.link AS feed_link, 
            f.uri AS feed_uri, f.favicon
            FROM member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            JOIN entry_to_tag e2t ON e.id = e2t.entry_id
            JOIN tag t ON e2t.tag_id = t.id
            WHERE m2f.member_id = ?
            AND t.name = ?
            ORDER BY e.date DESC";
        if (!is_null($limit)) $sql .= " LIMIT $offset, $limit";
        $result =& $db->getAll($sql, array($member_id, $tagname));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function get_entries_count_by_account_and_tagname($account, $tagname)/*{{{*/
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();
        $sql = "SELECT COUNT(e.id) AS CNT
            FROM member_to_feed m2f
            JOIN feed f ON m2f.feed_id = f.id
            JOIN entry e ON f.id = e.feed_id
            JOIN entry_to_tag e2t ON e.id = e2t.entry_id
            JOIN tag t ON e2t.tag_id = t.id
            WHERE m2f.member_id = ?
            AND t.name = ?";
        $result =& $db->getOne($sql, array($member_id, $tagname));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/
}
?>
