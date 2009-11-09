<?php
class SourcesAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $feed = DB_DataObject::factory('feed');
        $feeds = $feed->getListsByAccount($account);
        $request->setAttribute('sources', $feeds);
        return VIEW_SUCCESS;
    }
}
?>
