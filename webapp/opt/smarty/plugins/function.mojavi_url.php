<?php
function smarty_function_mojavi_url($params, &$smarty)
{
    return is_array($params) ? Controller::genURL($params) : SCRIPT_PATH;
}
?>