<?php
require_once 'DBUtils.php';
require_once 'MemberUtils.php';

class MemberToContentCategoryToFeedUtils
{
    function update_content_category_id_by_account_and_feed_id($cc_id, $account, $feed_id)
    {
        $member_id = MemberUtils::get_id_by_account($account);

        $db =& DBUtils::connect();

        $exists = MemberToContentCategoryToFeedUtils::is_exists_by_member_id_and_feed_id($member_id, $feed_id);

        if ($exists) {
            $fields = array(
                'content_category_id' => $cc_id
            );

            $result = $db->autoExecute(
                'member_to_content_category_to_feed',
                $fields,
                DB_AUTOQUERY_UPDATE,
                "member_id = $member_id AND feed_id = $feed_id"
            );
        } else {
            $fields = array(
                'content_category_id' => $cc_id,
                'member_id' => $member_id,
                'feed_id' => $feed_id
            );

            $result = $db->autoExecute(
                'member_to_content_category_to_feed',
                $fields,
                DB_AUTOQUERY_INSERT
            );
        }

        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }

    function is_exists_by_member_id_and_feed_id($member_id, $feed_id)
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT content_category_id FROM member_to_content_category_to_feed
            WHERE member_id = ?
            AND feed_id = ?';
        $result = $db->getOne($sql, array($member_id, $feed_id));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
?>
