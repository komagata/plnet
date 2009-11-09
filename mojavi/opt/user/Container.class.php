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
 * Container provides storage for user data.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package user
 * @since   2.0
 */
class Container
{

    /**
     * Create a new Container instance.
     *
     * @access public
     * @since  2.0
     */
    function Container ()
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

        $error = 'Container::load(&$authenticated, &$attributes, &$secure) ' .
                 'must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

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

        $error = 'Container::store(&$authenticated, &$attributes, &$secure) ' .
                 'must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

}

?>
