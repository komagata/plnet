<?php
require_once 'FriendUtils.php';

class FriendsAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->hasParameter('account')
            ? $request->getParameter('account') : null;
        $request->setAttribute('account', $account);

        $member = $user->getAttribute('member', GLU_NS);
        $request->setAttribute('member', $member);

        $friends = @FriendUtils::find_by_account($account);
        $request->setAttribute('friends_length', count($friends));
        $friends = array_slice($friends, 0, 5);
        $request->setAttribute('friends', $friends);
        return VIEW_SUCCESS;
    }
}
?>
