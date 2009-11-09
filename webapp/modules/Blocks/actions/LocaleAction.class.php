<?php
require_once 'HTML/AJAX/JSON.php';

class LocaleAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $messages = $request->getAttribute('messages');
        $haj = new HTML_AJAX_JSON;
        $m = $haj->encode($messages);
        header('Content-Type: text/javascript; charset=utf-8');
        echo "function msg(n){var m={$m};return m[n]}";
        return VIEW_NONE;
    }
}
?>
