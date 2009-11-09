<?php
class FootprintsView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Footprints.js');
        header('Content-Type: text/javascript; charset=utf-8');
        return $renderer;
    }
}
?>
