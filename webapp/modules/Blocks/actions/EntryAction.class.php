<?php
require_once 'EntryUtils.php';
require_once 'TagUtils.php';

class EntryAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member = DB_DataObject::factory('member');
        $member->get('account', $request->getParameter('account'));
        $request->setAttribute('member', $member);

        $account = $request->getParameter('account');
        $entry_id = $request->getParameter('entry_id');

        $entry = EntryUtils::get_by_account_and_id($account, $entry_id);
        $entry['tags'] = TagUtils::get_tags_by_entry_id($entry_id);
        $request->setAttribute('entry', $entry);

        return VIEW_SUCCESS;
    }
}
?>
