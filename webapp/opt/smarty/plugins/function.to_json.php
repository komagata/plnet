<?php
require_once 'HTML/AJAX/JSON.php';

function smarty_function_to_json($params, &$smarty)
{
    $haj =& new HTML_AJAX_JSON();
    return $haj->encode($params['from']);
}
?>
