<?php
class SiteMapView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('SiteMap.html');
        header('Content-Type: application/xml; charset=utf-8');
        return $renderer;
    }
}
?>
