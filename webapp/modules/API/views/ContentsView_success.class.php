<?php
require_once 'HTML/AJAX/JSON.php';

class ContentsView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Contents.js');
        $contents = $request->getAttribute('contents');
        $haj =& new HTML_AJAX_JSON();
        $renderer->setAttribute('contents', $haj->encode($contents));
        header('Content-Type: text/javascript; charset=utf-8');
        return $renderer;
    }
}
?>
