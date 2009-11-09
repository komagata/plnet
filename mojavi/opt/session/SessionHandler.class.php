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
 * SessionHandler provides a customizable way to store session data.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package session
 * @since   2.0
 */
class SessionHandler
{

    /**
     * Create a new SessionHandler instance.
     *
     * @access public
     * @since  2.0
     */
    function SessionHandler ()
    {

    }

    /**
     * Clean up session handler data.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @access public
     * @since  2.0
     */
    function cleanup ()
    {

    }

    /**
     * Close the session.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @access public
     * @since  2.0
     */
    function close ()
    {

        $error = 'SessionHandler::close() must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

    /**
     * Destroy the session.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param id A session id.
     *
     * @access public
     * @since  2.0
     */
    function destroy ($id)
    {

        $error = 'SessionHandler::destroy($id) must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

    /**
     * Garbage collection.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param lifetime The maximum lifetime of a session.
     *
     * @access public
     * @since  2.0
     */
    function gc ($lifetime)
    {

        $error = 'SessionHandler::gc($lifetime) must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

    /**
     * Open the session.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param path A session path.
     * @param name A session name.
     *
     * @access public
     * @since  2.0
     */
    function open ($path, $name)
    {

        $error = 'SessionHandler::open($path, $name) must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

    /**
     * Read session data.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param id A session id.
     *
     * @access public
     * @since  2.0
     */
    function read ($id)
    {

        $error = 'SessionHandler::read($id) must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

    /**
     * Write session data.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param id   A session id.
     * @param data The session data.
     *
     * @access public
     * @since  2.0
     */
    function write ($id, &$data)
    {

        $error = 'SessionHandler::write($id, &$data) must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

}

?>
