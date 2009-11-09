<?php
require_once LIB_DIR . 'MemberUtils.php';

class NewGlueAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $site = DB_DataObject::factory('site');
        $request->setAttribute('sites', $site->getNewSite(5));

        $member_num = MemberUtils::total();
        $request->setAttribute('member_num', $member_num);
        $request->setAttribute('yesterday', time() - 60 * 60 * 24);
        return VIEW_SUCCESS;
    }
}
?>
