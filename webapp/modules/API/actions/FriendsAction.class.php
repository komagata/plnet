<?php
require_once 'FriendUtils.php';
require_once 'LogUtils.php';

class FriendsAction extends Action
{
    function validate(&$controller, &$request, &$user)
    {
        $account = $request->hasParameter('account')
            ? $request->getParameter('account') : '';

        if (empty($account)) {
            return false;
        }
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $raw = $request->hasParameter('raw') ? true : false;
        $request->setAttribute('raw', $raw);

        $callback = $request->hasParameter('callback') ?
            $request->getParameter('callback') : false;
        $request->setAttribute('callback', $callback);

        $member = $user->getAttribute('member', GLU_NS);

        $friends = @FriendUtils::find_by_account($account);
        $request->setAttribute('friends', $friends);
        return VIEW_SUCCESS;
    }
}
?>
