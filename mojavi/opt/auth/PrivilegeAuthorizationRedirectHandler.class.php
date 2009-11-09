<?php
require_once AUTH_DIR . 'AuthorizationHandler.class.php';
class PrivilegeAuthorizationRedirectHandler extends AuthorizationHandler
{
    function execute (&$controller, &$request, &$user, &$action)
    {
        if (!$user->isAuthenticated()) {
            if ($controller->actionExists(AUTH_MODULE, AUTH_ACTION)) {
                $url = $controller->genURL(array(
                    MODULE_ACCESSOR => AUTH_MODULE, 
                    ACTION_ACCESSOR => AUTH_ACTION 
                ));
                //$controller->redirect($url);
                $controller->redirect('/login');
                return FALSE;
            }

            $error = 'Invalid configuration setting(s): ' .
                     'AUTH_MODULE (' . AUTH_MODULE . ') or ' .
                     'AUTH_ACTION (' . AUTH_ACTION . ')';
            trigger_error($error, E_USER_ERROR);
            exit;
        }

        $privilege = $action->getPrivilege($controller, $request, $user);

        if ($privilege != NULL && !isset($privilege[1])) {
            $privilege[] = 'org.mojavi';
        }

        if ($privilege != NULL &&
           !$user->hasPrivilege($privilege[0], $privilege[1])) {
            if ($controller->actionExists(SECURE_MODULE, SECURE_ACTION)) {
                $controller->forward(SECURE_MODULE, SECURE_ACTION);
                return FALSE;
            }

            $error = 'Invalid configuration setting(s): ' .
                     'SECURE_MODULE (' . SECURE_MODULE . ') or ' .
                     'SECURE_ACTION (' . SECURE_ACTION . ')';

            trigger_error($error, E_USER_ERROR);
            exit;
        }
        return TRUE;
    }
}
?>
