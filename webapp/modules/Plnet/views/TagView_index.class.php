<?php
class TagView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Tag.html');
        return $renderer;
    }
}
?>
