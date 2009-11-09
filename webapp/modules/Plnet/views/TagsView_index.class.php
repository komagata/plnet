<?php
class TagsView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Tags.html');
        return $renderer;
    }
}
?>
