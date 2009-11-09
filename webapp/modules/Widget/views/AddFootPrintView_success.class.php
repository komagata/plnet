<?php
class EntriesView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Entries.js');
        header('Content-Type: text/javascript; charset=utf-8');
        return $renderer;
    }
}
?>
