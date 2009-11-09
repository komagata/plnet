<?php
class FeedView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $format = $request->getParameter('format');
        $renderer =& RendererUtils::getSmartyRenderer();

        switch ($format) {
        case 'rss2':
            $renderer->setTemplate('RSS2.html');
            header('Content-Type: application/xml; charset=utf-8');
            break;
        case 'atom03':
            $renderer->setTemplate('Atom03.html');
            header('Content-Type: application/xml; charset=utf-8');
            break;
        case 'atom10':
            $renderer->setTemplate('Atom10.html');
            header('Content-Type: application/xml; charset=utf-8');
            break;
        case 'json':
            $renderer->setTemplate('Json.js');
            $feed = $request->getAttribute('feed');
            $haj =& new HTML_AJAX_JSON();
            $renderer->setAttribute('feed', $haj->encode($feed));
            header('Content-Type: text/javascript; charset=utf-8');
            break;
        case 'rss':
        default:
            $renderer->setTemplate('RSS.html');
            header('Content-Type: application/xml; charset=utf-8');
            break;
        }
        return $renderer;
    }
}
?>
