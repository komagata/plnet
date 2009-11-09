<?php
require_once 'EntryUtils.php';
require_once 'TagUtils.php';
require_once 'HTML/AJAX/JSON.php';

class EntriesAction extends Action
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

        $entries = EntryUtils::get_entries_by_account($account, $count);

        foreach ($entries as $key => $entry) {
            $entry['tags'] = TagUtils::get_tags_by_entry_id($entry['id']);
            $entry['src'] = $entry['uri'];
            $entry['uri'] = SCRIPT_PATH . "{$account}/{$entry['id']}";
            $entries[$key] = $entry;
        }

        $haj =& new HTML_AJAX_JSON();
        $request->setAttribute('entries', $haj->encode($entries));
        return VIEW_SUCCESS;
    }
}
?>
