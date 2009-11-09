<?php
class ActivateView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('ActivateError.html');
        return $renderer;
    }
}
?>
