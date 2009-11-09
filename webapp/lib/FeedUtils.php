<?php
require_once 'DBUtils.php';
require_once 'ContentCategoryUtils.php';

class FeedUtils
{
    function get_feed_by_id($feed_id)
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT * FROM feed WHERE id = ?';
        $result = $db->getRow($sql, array($feed_id));
        if (DB::isError($result)) {
            trigger_error('FeedUtils::get_feed_by_id(): '.
            $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }

    function get_feeds_by_account($account)
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT f.id, f.title, f.uri, f.link, f.favicon
            FROM member m
            JOIN member_to_feed m2f ON m.id = m2f.member_id
            JOIN feed f ON m2f.feed_id = f.id
            WHERE m.account = ?
            ORDER BY f.id';

        $result = $db->getAll($sql, array($account));
        if (DB::isError($result)) {
            trigger_error('FeedUtils::get_feeds_by_account(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }

    function find_with_content_category_by_account($account)
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT cc.id AS category_id, cc.name AS category_name, mcf.feed_id
            FROM member_to_content_category_to_feed mcf
            JOIN member m ON mcf.member_id = m.id
            JOIN content_category cc ON mcf.content_category_id = cc.id
            WHERE m.account = ?';

        $result = $db->getAll($sql, array($account));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
            $result->toString(), E_USER_ERROR);
            return false;
        }

        $mcfs = $result;
        $other = ContentCategoryUtils::get(PLNET_OTHER_CATEGORY_ID);

        $feeds = FeedUtils::get_feeds_by_account($account);

        $feeds_with_category = array();
        foreach ($feeds as $key => $feed) {
            foreach ($mcfs as $i => $mcf) {
                if ($feed['id'] == $mcf['feed_id']) {
                    $feed['category_id'] = $mcf['category_id'];
                    $feed['category_name'] = $mcf['category_name'];
                }
            }
            if (!isset($feed['category_id'])) {
                $feed['category_id'] = $other['id'];
                $feed['category_name'] = $other['name'];
            }
            $feeds_with_category[] = $feed;
        }

        return $feeds_with_category;
    }
}
?>
