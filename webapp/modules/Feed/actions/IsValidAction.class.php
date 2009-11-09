<?php
require_once LIB_DIR . 'FeedParser.php';

class IsValidAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $uri = $request->getParameter('uri');
        $feed = new FeedParser($uri);
        $response = '';
        if ($feed->isValid() === false) {
            $response = 'false';
        } else {
            $response = 'true';
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo $response;
        return VIEW_NONE;
    }
}
?>
