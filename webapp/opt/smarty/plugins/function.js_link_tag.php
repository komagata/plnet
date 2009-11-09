<?php
function smarty_function_js_link_tag($params, &$smarty)
{
    return "<script type=\"text/javascript\" src=\"scripts/{$params['name']}.js\"></script>";
}
?>
