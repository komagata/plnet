<?php
require_once LIB_DIR . 'MemberUtils.php';

class NewPlnetAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $site = DB_DataObject::factory('site');
        $sql = 'SELECT m.account, s.title, s.description,
            UNIX_TIMESTAMP(m.createdtime) AS createdtime
            FROM member m
            JOIN source src ON m.id = src.member_id
            JOIN site s ON m.id = s.member_id
            GROUP BY m.id
            HAVING COUNT(s.id) > 0
            ORDER BY m.createdtime DESC
            LIMIT 16';

        $site->query($sql);
        $sites = array();
        while ($site->fetch()) {
            $sites[] = $site;
        }
        $request->setAttribute('sites', $sites);

        $request->setAttribute('title', PLNET_FEED_NEW_PLNET_TITLE);
        $request->setAttribute('description', PLNET_FEED_NEW_PLNET_DESCRIPTION);

        $member_num = MemberUtils::total();
        $request->setAttribute('member_num', $member_num);
        return VIEW_SUCCESS;
    }
}
?>
