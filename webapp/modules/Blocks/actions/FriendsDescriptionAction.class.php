<?php
require_once 'FriendUtils.php';

class FriendsDescriptionAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->hasParameter('account') 
            ? $request->getParameter('account') : null;
        $request->setAttribute('account', $account);

        $member = $user->getAttribute('member', GLU_NS);
        $request->setAttribute('member', $member);

        $friends = @FriendUtils::find_by_account($account);
        $request->setAttribute('friends', $friends);
        $request->setAttribute('friends_length', count($friends));
        return VIEW_SUCCESS;
    }
}
?>
