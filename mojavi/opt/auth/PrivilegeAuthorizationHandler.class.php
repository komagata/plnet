<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Mojavi package.                                  |
// | Copyright (c) 2003 Sean Kerr.                                             |
// |                                                                           |
// | For the full copyright and license information, please view the COPYRIGHT |
// | file that was distributed with this source code. If the COPYRIGHT file is |
// | missing, please visit the Mojavi homepage: http://www.mojavi.org          |
// +---------------------------------------------------------------------------+

// include dependencies
require_once(AUTH_DIR . 'AuthorizationHandler.class.php');

/**
 * PrivilegeAuthorizationHandler determines the method of authorization by
 * checking for a single privilege, which can also be contained in a namespace.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package auth
 * @since   2.0
 */
class PrivilegeAuthorizationHandler extends AuthorizationHandler
{

    /**
     * Create a new PrivilegeAuthorizationHandler instance.
     *
     * @access public
     * @since  2.0
     */
    function PrivilegeAuthorizationHandler ()
    {

    }

    /**
     * Determine the user authorization status for an action request by checking
     * verifying against a required privilege.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param Controller A Controller instance.
     * @param Request    A Request instance.
     * @param User       A User instance.
     * @param Action     An Action instance.
     *
     * @access public
     * @since  2.0
     */
    function execute (&$controller, &$request, &$user, &$action)
    {

        if (!$user->isAuthenticated())
        {

            if ($controller->actionExists(AUTH_MODULE, AUTH_ACTION))
            {

                // this action requires authentication, and the user isn't
                // authenticated
                $controller->forward(AUTH_MODULE, AUTH_ACTION);

                return FALSE;

            }

            // cannot find authentication action
            $error = 'Invalid configuration setting(s): ' .
                     'AUTH_MODULE (' . AUTH_MODULE . ') or ' .
                     'AUTH_ACTION (' . AUTH_ACTION . ')';

            trigger_error($error, E_USER_ERROR);

            exit;

        }

        $privilege = $action->getPrivilege($controller, $request, $user);

        if ($privilege != NULL && !isset($privilege[1]))
        {

            $privilege[] = 'org.mojavi';

        }

        if ($privilege != NULL &&
           !$user->hasPrivilege($privilege[0], $privilege[1]))
        {

            // user doesn't have access
            if ($controller->actionExists(SECURE_MODULE, SECURE_ACTION))
            {

                $controller->forward(SECURE_MODULE, SECURE_ACTION);

                return FALSE;

            }

            // cannot find secure action
            $error = 'Invalid configuration setting(s): ' .
                     'SECURE_MODULE (' . SECURE_MODULE . ') or ' .
                     'SECURE_ACTION (' . SECURE_ACTION . ')';

            trigger_error($error, E_USER_ERROR);

            exit;

        }

        // user is authenticated, and has the required privilege or a privilege
        // is not required

        return TRUE;

    }

}

?>
