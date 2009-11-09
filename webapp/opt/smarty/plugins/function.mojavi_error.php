<?php
function smarty_function_mojavi_error($params, &$smarty)
{
    $controller =& Controller::getInstance();
    $request =& $controller->request;
    if ($request->hasError($params['name'])) {
        return $request->getError($params['name']);
    }
    return null;
}
?>