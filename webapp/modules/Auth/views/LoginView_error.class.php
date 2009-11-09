<?php
class LoginView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $renderer =& RendererUtils::getSmartyRenderer($controller, $request, $user);
        $renderer->setTemplate('Input.html');
        return $renderer;
    }
}
?>
