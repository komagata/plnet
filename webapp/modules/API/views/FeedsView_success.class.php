<?php
require_once 'HTML/AJAX/JSON.php';

class FeedsView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Feeds.js');
        $haj =& new HTML_AJAX_JSON();
        $feeds = $request->getAttribute('feeds');
        $renderer->setAttribute('feeds', $haj->encode($feeds));
        header('Content-Type: text/javascript; charset=utf-8');
        return $renderer;
    }
}
?>
