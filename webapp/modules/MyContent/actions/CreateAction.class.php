<?php
require_once LIB_DIR . 'FeedParser.php';

class CreateAction extends Action
{
    function isSecure()
    {
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $mode = $request->hasParameter('mode') ? $request->getParameter('mode') : null;
        if ($mode === 'create') {
            $uri = $request->getParameter('uri');
            $back = $request->getParameter('back');

            $feed =& new FeedParser($uri);        
            $res = $feed->parse();
            if ($res === false) {
                return VIEW_ERROR;
            }
            return VIEW_INPUT; 
        } else {
            return VIEW_NONE;
        }
    }
}
?>
