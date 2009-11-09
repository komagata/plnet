<?php
require_once 'HTML/AJAX/JSON.php';

class SourcesView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();

        switch ($request->getAttribute('format')) {
        case 'json':
            $renderer->setTemplate('Sources.js');
            $entries = $request->getAttribute('entries');
            $haj =& new HTML_AJAX_JSON();
            $renderer->setAttribute('entries', $haj->encode($entries));
            header('Content-Type: text/javascript; charset=utf-8');
            break;
        case 'rss2':
        default:
            $renderer->setTemplate('Sources.xml');
            header('Content-Type: application/xml; charset=utf-8');
            break;
        }
        return $renderer;
    }
}
?>
