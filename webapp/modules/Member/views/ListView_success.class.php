<?php
class ListView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('List.html');
        return $renderer;
    }
}
?>
