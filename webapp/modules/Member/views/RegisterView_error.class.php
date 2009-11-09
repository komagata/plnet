<?php
class RegisterView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate('Register.html');
        return $renderer;
    }
}
?>
