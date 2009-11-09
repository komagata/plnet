<?php
require_once 'FeedUtils.php';
require_once 'URIUtils.php';

class IndexAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $entry_id = $request->hasParameter('entry_id') 
            ? $request->getParameter('entry_id') : null;
        $request->setAttribute('entry_id', $entry_id);

        $page = $request->hasParameter('page') 
            ? $request->getParameter('page') : '';
        $request->setAttribute('page', $page);

        $display = $request->hasParameter('display') 
            ? $request->getParameter('display') : null;
        $request->setAttribute('display', $display);

        $tag = $request->hasParameter('tag') 
            ? $request->getParameter('tag') : false;
        $request->setAttribute('tag', $tag);

        $source_id = $request->hasParameter('source_id') 
            ? $request->getParameter('source_id') : null;
        $request->setAttribute('source_id', $source_id);
        if ($source_id) {
            $feed = FeedUtils::get_feed_by_id($source_id);
            $request->setAttribute('feed_title', $feed['title']);
        }

        $member = DB_DataObject::factory('member');
        $member->account = $request->getParameter('account');
        if ($member->count() === 0) {
            Controller::redirect('404.html');
        }

        $member = DB_DataObject::factory('member');
        $member->get('account', $request->getParameter('account'));
        $request->setAttribute('member', $member);
        $request->setAttribute('account', $member->account);

        // entry check
        $entry = DB_DataObject::factory('entry');
        if (!$entry->entryIsExistsByMemberId($member->id)) {
            return VIEW_ERROR;
        }

        // design
        $design = DB_DataObject::factory('design');
        $design->get('id', $member->design_id);
        $request->setAttribute('design', $design);

        // site
        $site = DB_DataObject::factory('site');
        $site->get('member_id', $member->id);
        $request->setAttribute('site', $site);

        if ($display == 'profile' && !$site->show_profile) {
            $controller->redirect('/404.html');
            return VIEW_NONE;
        }

        return VIEW_INDEX;
    }
}
?>
