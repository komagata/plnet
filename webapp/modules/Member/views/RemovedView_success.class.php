<?php
class RemovedView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Removed.html');
        return $renderer;
    }
}
?>
