<?php
class IndexView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate("Index.html");
        return $renderer;
    }
}
?>
