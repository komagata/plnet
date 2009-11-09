<?php
class LogoutAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        if($user->isAuthenticated()) {
            $user->setAuthenticated(false);
            $user->clearPrivileges();
            $user->removeAttribute('member', GLU_NS);
            setcookie('token', '', time() - 3600);
        }
        Controller::redirect(SCRIPT_PATH);
        return VIEW_NONE;
    }
}
?>
