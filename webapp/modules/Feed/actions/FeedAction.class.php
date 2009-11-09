<?php
require_once 'EntryUtils.php';
require_once 'TagUtils.php';
require_once 'HTML/AJAX/JSON.php';

class FeedAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $request->setAttribute('account', $account);

        $count = $request->hasParameter('count') ? $request->getParameter('count') : 16;
        $callback = $request->hasParameter('callback') ? $request->getParameter('callback') : false;
        $request->setAttribute('callback', $callback);

        $raw = $request->hasParameter('raw') ? true : false;
        $request->setAttribute('raw', $raw);

        $member = DB_DataObject::factory('member');
        $member->get('account' , $account);

        $site = DB_DataObject::factory('site');
        $site->get('member_id', $member->id);

        $feed = array();
        $feed['uri'] = SCRIPT_PATH . "{$member->account}/";
        $feed['title'] = $site->title;
        $feed['description'] = $site->description;
        $feed['author'] = $member->account;

        $entries = EntryUtils::get_entries_by_account($account, $count);

        foreach ($entries as $key => $entry) {
            $entry['tags'] = TagUtils::get_tags_by_entry_id($entry['id']);
            $entries[$key] = $entry;
        }

        $feed = $feed + $entries;
        $request->setAttribute('feed', $feed);
        $request->setAttribute('entries', $entries);
        return VIEW_SUCCESS;
    }
}
?>
