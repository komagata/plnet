<?php
class EntryView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Entry.html');
        return $renderer;
    }
}
?>
