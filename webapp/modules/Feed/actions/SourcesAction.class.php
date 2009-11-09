<?php
require_once 'EntryUtils.php';
require_once 'FeedUtils.php';

class SourcesAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $request->setAttribute('account', $account);

        $feed_id = $request->getParameter('source_id');
        $request->setAttribute('feed_id', $feed_id);

        $callback = $request->hasParameter('callback') 
            ? $request->getParameter('callback') : false;
        $request->setAttribute('callback', $callback);

        $raw = $request->hasParameter('raw') ? true : false;
        $request->setAttribute('raw', $raw);

        $format = $request->hasParameter('format') 
            ? $request->getParameter('format') : false;
        $request->setAttribute('format', $format);

        $feed = FeedUtils::get_feed_by_id($feed_id);
        $site = array(
            'title' => $feed['title'],
            'description' => $feed['description']
        );
        $request->setAttribute('feed', $feed);

        $entries = EntryUtils::get_entries_by_account_and_feed_id($account, $feed_id);
        foreach ($entries as $key => $entry) {
            $entry['src'] = $feed['uri'];
            $entry['uri'] = SCRIPT_PATH . "{$account}/source/{$entry['id']}";
            $entries[$key] = $entry;
        }

        $request->setAttribute('entries', $entries);
        return VIEW_SUCCESS;
    }
}
?>
