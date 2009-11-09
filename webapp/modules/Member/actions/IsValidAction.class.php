<?php
class IsValidAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $member = DB_DataObject::factory('member');        
        $member->account = $account;

        $mt = DB_DataObject::factory('member_temporary');
        $mt->account = $account;

        if ($member->count() > 0 or $mt->count() > 0) {
            $response = 'false';
        } else {
            $response = 'true';
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo $response;
        return VIEW_NONE;
    }
}
?>
