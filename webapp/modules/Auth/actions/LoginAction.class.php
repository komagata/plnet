<?php
class LoginAction extends Action
{
    var $layout = 'Public';

    function initialize(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = 'Plnet &gt; '.msg('login');
        return true;
    }

    function getRequestMethods() { return REQ_POST; }

    function execute(&$controller, &$request, &$user)
    {

        $account  = $request->getParameter('account');
        $password = $request->getParameter('password');
        $autologin = $request->hasParameter('autologin')
            ? $request->getParameter('autologin') : null;

        $member = DB_DataObject::factory('member');
        $member->get('account', $account);
        if ($member->password == sha1($password)) {
            $user->setAuthenticated(true);
            $user->addPrivilege('member', GLU_NS);
            $user->setAttribute('member', $member, GLU_NS);

            if ($autologin) {
                setcookie(
                    'token',
                    $member->id . '-' . $member->password,
                    time() + PLNET_LOGIN_LIFETIME
                );
            } else {
                setcookie('token', '', time() - 3600);
            }

            Controller::redirect(SCRIPT_PATH . 'setting/feed');
            return VIEW_NONE;
        } else {
            $request->setError('login', ERROR_LOGIN);
            return VIEW_ERROR;
        }
    }
}
?>
