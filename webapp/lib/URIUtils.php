<?php
class URIUtils {
    function redirect($modName, $actName, $opt = null)
    {
        $params = array(MODULE_ACCESSOR => $modName, ACTION_ACCESSOR => $actName);
        if (is_array($opt)) $params = array_merge($params, $opt);
        Controller::redirect(Controller::genURL($params));
    }
}
?>
