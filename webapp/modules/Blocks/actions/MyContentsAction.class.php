<?php
class MyContentsAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $feed = DB_DataObject::factory('feed');
        $feeds = $feed->getListsByAccount($account);
        $request->setAttribute('mycontents', $feeds);
        return VIEW_SUCCESS;
    }
}
?>
