<?php
require_once LIB_DIR . 'EntryUtils.php';
require_once LIB_DIR . 'SiteUtils.php';

class TagsAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $request->setAttribute('account', $account);

        $format = $request->hasParameter('format') ?
            $request->getParameter('format') : 'rss2';
        $request->setAttribute('format', $format);

        $raw = $request->hasParameter('raw') ? true : false;
        $request->setAttribute('raw', $raw);

        $count = $request->hasParameter('count') ?
            $request->getParameter('count') : '16';
        $request->setAttribute('count', $count);

        $callback = $request->hasParameter('callback') ?
            $request->getParameter('callback') : false;
        $request->setAttribute('callback', $callback);

        $tag = $request->getParameter('tag');
        $request->setAttribute('tag', $tag);

        $site = SiteUtils::get_by_account($account);
        $request->setAttribute('site', $site);

        $entries = EntryUtils::get_entries_by_account_and_tagname($account, $tag, $count);
        $request->setAttribute('entries', $entries);
        return VIEW_SUCCESS;
    }
}
?>
