<?php
class PageNotFoundView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('PageNotFound.html');
        return $renderer;
    }
}
?>
