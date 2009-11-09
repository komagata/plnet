<?php
class AutoLoginFilter extends Filter
{
    function execute (&$filterChain, &$controller, &$request, &$user)
    {
        $loaded =& $request->getAttribute('AutoLoginFilter');

        if ($loaded == null) {
            $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

            if ($token and !$user->isAuthenticated()) {
                list($id, $password) = split('-', $token);
                $member = DB_DataObject::factory('member');
                $member->get($id);

                if ($member->password == $password) {
                    $user->setAuthenticated(true);
                    $user->addPrivilege('member', GLU_NS);
                    $user->setAttribute('member', $member, GLU_NS);

                    // added cookie liftime for footprint
/*
                    setcookie(
                        'token', $token, time() + PLNET_LOGIN_LIFETIME
                    );
*/
                }
            }
            $filterChain->execute($controller, $request, $user);
        } else {
            $filterChain->execute($controller, $request, $user);
        }
    }
}
?>
