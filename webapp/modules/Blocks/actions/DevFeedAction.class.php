<?php
require_once LIB_DIR . 'FeedParser.php';

class DevFeedAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $feed =& new FeedParser(DEV_FEED_URI);
        $res = @$feed->parse();
        if ($res === false) {
            return VIEW_NONE;
        }

        $yesterday = time() - 60 * 60 * 24;

        $items = $feed->getItems();
        $devfeeds = array();
        if (is_array($items)) $devfeeds = array_slice($items, 0, 5);

        foreach ($devfeeds as $index => $devfeed) {
            $devfeeds[$index]['new'] = $devfeed['date'] > $yesterday
                ? true : false;
        }

        $request->setAttribute('devfeeds', $devfeeds);
        $request->setAttribute('yesterday', time() - 60 * 60 * 24);
        return VIEW_SUCCESS;
    }
}
?>
