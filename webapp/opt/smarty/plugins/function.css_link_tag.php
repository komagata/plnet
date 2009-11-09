<?php
function smarty_function_css_link_tag($params, &$smarty)
{
    $file = $params['name'];
    unset($params['name']);
    $tag = '<link ';
    $params['rel'] = 'stylesheet';
    $params['type'] = 'text/css';
    $params['href'] = 'styles/'.$file.'.css?'.time();
    foreach ($params as $key => $value) {
        $tag .= $key.'="'.$value.'" ';
    }
    $tag .= '/>';
    return $tag;
}
?>
