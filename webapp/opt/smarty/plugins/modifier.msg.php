<?php
function smarty_modifier_msg($name)
{
    $c =& Controller::getInstance();
    $messages = $c->request->getAttribute('messages');
    $messages[$name] = str_replace('\n', "\n", $messages[$name]);
    return $messages[$name];
}
?>
