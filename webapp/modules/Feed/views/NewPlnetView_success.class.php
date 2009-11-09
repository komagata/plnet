<?php
class NewPlnetView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('NewPlnet.xml');
        header('Content-Type: application/xml; charset=utf-8');
        return $renderer;
    }
}
?>
