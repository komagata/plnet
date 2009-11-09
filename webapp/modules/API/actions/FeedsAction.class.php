<?php
require_once 'FeedUtils.php';
require_once 'MemberToContentCategoryToFeedUtils.php';

class FeedsAction extends RESTAction
{
    function get(&$controller, &$request, &$user)
    {
        $account = $request->hasParameter('account')
            ? $request->getParameter('account') : null;

        $feeds = FeedUtils::find_with_content_category_by_account($account);
        foreach ($feeds as $i => $feed) {
            $feeds[$i]['category_name'] = msg($feed['category_name']);
        }
        $request->setAttribute('feeds', $feeds);
        return VIEW_SUCCESS;
    }

    function put(&$controller, &$request, &$user)
    {
        $account = $request->hasParameter('account')
            ? $request->getParameter('account') : null;
        $putted_feeds = $request->hasParameter('feeds')
            ? $request->getParameter('feeds') : null;

        $feeds = FeedUtils::find_with_content_category_by_account($account);
        $diff_feeds = $this->diff_feeds($putted_feeds, $feeds);
        $result = true;
        foreach ($diff_feeds as $diff_feed) {
            $res = MemberToContentCategoryToFeedUtils::update_content_category_id_by_account_and_feed_id($diff_feed['category_id'], $account, $diff_feed['id']);
            if (!$res) $result = false;
        }

        header('Content-Type: text/javascript; charset=utf-8');
        if ($result) {
            echo 'true';
        } else {
            echo 'false';
        }
        return VIEW_NONE;
    }


    function diff_feeds($sorted ,$src)
    {
        $diff = array();
        foreach ($sorted as $key => $feed) {
            foreach ($src as $i => $f) {
                if ($feed['id'] == $f['id']
                and $feed['category_id'] != $f['category_id']) {
                    $diff[] = $feed;
                }
            }
        }
        return $diff;
    }
}
?>
