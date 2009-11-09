<?php
class ProfileAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');

        $member = DB_DataObject::factory('member');
        $member->get('account', $account);
        if ($member->photo) {
            $member->photo_url = SCRIPT_PATH.'photo.php?member_id='.$member->id;
        }
        $request->setAttribute('member', $member);
        return VIEW_SUCCESS;
    }
}
?>
