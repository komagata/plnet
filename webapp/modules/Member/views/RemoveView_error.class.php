<?php
class RemoveView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('RemoveError.html');
        return $renderer;
    }
}
?>
