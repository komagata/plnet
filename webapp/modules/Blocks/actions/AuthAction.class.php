<?php
class AuthAction extends Action
{
    function validate(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $password = $request->getParameter('password');

        if (strlen($account) == 0) {
            $request->setError('error_auth', 1);
        } elseif (strlen($password) == 0) {
            $request->setError('error_auth', 1);
        }

        if (count($request->getErrors()) == 0) {
            return true;
        } else {
            return false;
        }
    }

    function execute(&$controller, &$request, &$user)
    {
        $authtype = $request->getParameter('authtype');
        $account = $request->getParameter('account');
        $password = $request->getParameter('password');
        $password2 = $request->hasParameter('password2')
            ? $request->getParameter('password2') : '';

        // for admin
        if ($account == ADMIN_ACCOUNT
            && $password == ADMIN_PASSWORD) {
            $user->setAuthenticated(true);
            $user->addPrivilege('admin', DEP_NS);
            URIUtils::redirect('AdminTop', 'Index');
            return VIEW_NONE;
        }

        $customer = DB_DataObject::factory('customer');

        // login
        if ($authtype == 'login') {

            $customer->get('account', $account);

            // valied account or password
            if ($password != $customer->password) {
                $request->setError('error_auth', 1);
                URIUtils::redirect('Top', 'Index', $request->getErrors());
                return VIEW_NONE;
            }

        // register
        } else {
            // password not verify
            if ($password != $password2) {
                $request->setError('authtype', 'register');
                $request->setError('error_auth', 3);
                URIUtils::redirect('Top', 'Index', $request->getErrors());
                return VIEW_NONE;
            }

            // account already taken
            if ($customer->get('account', $account) == 1) {
                $request->setError('authtype', 'register');
                $request->setError('error_auth', 2);
                URIUtils::redirect('Top', 'Index', $request->getErrors());
                return VIEW_NONE;
            }

            $customer = DB_DataObject::factory('customer');
            $customer->account = $account;
            $customer->password = $password;

            if ($customer->insert() == false) {
                trigger_error('AuthAction::execute(): Failed to register customer.', E_USER_ERROR);
                return VIEW_NONE;
            }
        }

        $user->setAuthenticated(true);
        $user->addPrivilege('customer', DEP_NS);
        $user->setAttribute('customer', $customer, DEP_NS);
        URIUtils::redirect('Top', 'Index', $request->getErrors());
        return VIEW_NONE;
    }

    function handleError(&$controller, &$request, &$user)
    {
        URIUtils::redirect('Top', 'Index', $request->getErrors());
        return VIEW_NONE;
    }

    function getRequestMethods()
    {
        return REQ_POST;
    }
}
?>
