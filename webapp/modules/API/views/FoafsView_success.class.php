<?php
require_once 'HTML/AJAX/JSON.php';

class FoafsView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Foafs.js');
        $foafs = $request->getAttribute('foafs');
        $haj =& new HTML_AJAX_JSON();
        $renderer->setAttribute('foafs', $haj->encode($foafs));
        header('Content-Type: text/javascript; charset=utf-8');
        return $renderer;
    }
}
?>
