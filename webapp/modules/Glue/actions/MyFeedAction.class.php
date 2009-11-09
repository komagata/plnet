<?php
require_once 'HTML/AJAX/JSON.php';
require_once LIB_DIR . 'FeedParser.php';

class MyFeedAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member = $user->getAttribute('member', GLU_NS);

        // my feeds
        $source = DB_DataObject::factory('source');
        $source->whereAdd('member_id = ' . $source->escape($member->id));
        if ($request->hasParameter('id')) {
            $source->whereAdd('id = ' . $source->escape($request->getParameter('id')));
        }
        $source->find();
        $feeds = array();
        while ($source->fetch()) {
            $rss = new FeedParser($source->uri);
            $rss->parse();
            foreach ($rss->getItems() as $item) {
                $feed['id']          = $source->id;
                $feed['title']       = $item['title'];
                $feed['link']        = $item['link'];
                $feed['description'] = isset($item['description']) ? $item['description'] : '';
                $feed['date']        = (isset($item['dc:date']) ? $item['dc:date'] : (isset($item['date']) ? $item['date'] : ''));
                $feeds[] = $feed;
            }
        }

        $haj = new HTML_AJAX_JSON();
        $output = $haj->encode($feeds);

        header('Content-Type: application/x-javascript; charset=utf-8');
        echo $output;
        return VIEW_NONE;
    }
}
?>
