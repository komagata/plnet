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
require_once(USER_DIR . 'Container.class.php');

/**
 * SessionContainer stores data in a session.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package user
 * @since   2.0
 */
class SessionContainer extends Container
{

    /**
     * Create a new SessionContainer instance.
     *
     * @access public
     * @since  2.0
     */
    function SessionContainer ()
    {

    }

    /**
     * Load user data.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param bool  The authenticated status.
     * @param array An associative array of attributes.
     * @param mixed Security related data.
     *
     * @access public
     * @since  2.0
     */
    function load (&$authenticated, &$attributes, &$secure)
    {

        if (ini_get('session.auto_start') != 1)
        {

            session_start();

        }

        if (!isset($_SESSION['attributes']))
        {

            $authenticated = FALSE;
            $attributes    = array();
            $secure        = array();

        } else
        {

            // can't use a reference here
            $authenticated = $_SESSION['authenticated'];
            $attributes    = $_SESSION['attributes'];
            $secure        = $_SESSION['secure'];

        }

        $_SESSION['authenticated'] =& $authenticated;
        $_SESSION['attributes']    =& $attributes;
        $_SESSION['secure']        =& $secure;

    }

    /**
     * Store user data.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param bool  The authenticated status.
     * @param array An associative array of attributes.
     * @param mixed Security related data.
     *
     * @access public
     * @since  2.0
     */
    function store (&$authenticated, &$attributes, &$secure)
    {

        // we don't store because we're accessing references when we load

    }

}

?>
