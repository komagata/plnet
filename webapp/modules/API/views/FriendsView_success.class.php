<?php
require_once 'HTML/AJAX/JSON.php';

class FriendsView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Friends.js');
        $friends = $request->getAttribute('friends');
        $haj =& new HTML_AJAX_JSON();
        $renderer->setAttribute('friends', $haj->encode($friends));
        header('Content-Type: text/javascript; charset=utf-8');
        return $renderer;
    }
}
?>
