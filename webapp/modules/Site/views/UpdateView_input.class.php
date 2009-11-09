<?php
class UpdateView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Update.html');
        return $renderer;
    }
}
?>
