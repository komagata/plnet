<?php
class CreateView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Create.html');
        return $renderer;
    }
}
?>
