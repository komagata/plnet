<?php
require_once 'EntryUtils.php';

class ArchivesAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $request->setAttribute('account', $account);
        $archives = EntryUtils::get_archives_by_account($account);
        $request->setAttribute('archives', $archives);
        return VIEW_SUCCESS;
    }
}
?>
