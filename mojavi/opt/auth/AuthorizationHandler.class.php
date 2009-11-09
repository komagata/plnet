<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Mojavi package.                                  |
// | Copyright (c) 2003 Sean Kerr.                                             |
// |                                                                           |
// | For the full copyright and license information, please view the COPYRIGHT |
// | file that was distributed with this source code. If the COPYRIGHT file is |
// | missing, please visit the Mojavi homepage: http://www.mojavi.org          |
// +---------------------------------------------------------------------------+

/**
 * AuthorizationHandler determines the method of authorization for a user.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package auth
 * @since   2.0
 */
class AuthorizationHandler
{

    /**
     * Create a new AuthorizationHandler instance.
     *
     * @access public
     * @since  2.0
     */
    function AuthorizationHandler ()
    {

    }

    /**
     * Determine the user authorization status for an action request.
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

        $error = 'AuthorizationHandler::execute(&$controller, &$request, ' .
                 '&$user, &$action) must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

}

?>
