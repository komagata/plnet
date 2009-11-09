<?php
require_once LIB_DIR . 'CachedFeed.php';

class ReadAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $uri = $request->getParameter('uri');
        $cf = new CachedFeed($uri);
        $cf->parse();

        header('Content-Type: application/x-javascript; charset=utf-8');
        echo $cf->toJSON();
        return VIEW_NONE;
    }
}
?>
