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
require_once(SESSION_DIR . 'SQLSessionHandler.class.php');

/**
 * MySQLSessionHandler stores sessions in a MySQL database.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package session
 * @since   2.0
 */
class MySQLSessionHandler extends SQLSessionHandler
{

    /**
     * An associative array of database connection parameters.
     *
     * @access private
     * @since  2.0
     * @type   array
     */
    var $params;

    /**
     * Whether or not the connection must be persistent.
     *
     * @access private
     * @since  2.0
     * @type   bool
     */
    var $persistent;

    /**
     * Create a new MySQLSessionHandler instance.
     *
     * <br/><br/>
     *
     * <b>Available connection-related parameters:</b>
     *
     * <ul>
     *     <li><b>db</b> - the database name</li>
     *     <li><b>host</b> - the database host</li>
     *     <li><b>user</b> - the database user</li>
     *     <li><b>password</b> - the database password</li>
     * </ul>
     *
     * <b>Available storage-related parameters:</b>
     *
     * <ul>
     *     <li><b>table</b>       - the table in which data will be stored</li>
     *     <li><b>data_col</b>    - the column where session data will be
     *                              stored</li>
     *     <li><b>id_col</b>      - the column where the session id will be
     *                              stored</li>
     *     <li><b>ts_col</b>      - the column where the session timestamp will be
     *                              stored</li>
     *     <li><b>crc32_check</b> - whether or not to use a crc32 hash to
     *                              compare session data</li>
     * </ul>
     *
     * @param array      An associative array of connection-related parameters.
     * @param array      An associative array of storage-related parameters.
     * @param persistent Whether or not the connection must be persistent.
     */
    function MySQLSessionHandler ($connParams = NULL, $storParams = NULL,
                                    $persistent = FALSE)
    {

        parent::SQLSessionHandler($storParams);

        $this->params     = $connParams;
        $this->persistent = $persistent;

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

        if ($this->conn != NULL)
        {

            if (@mysql_close($this->conn) === FALSE)
            {

                $error = 'Failed to close MySQL session -- error: ' .
                         mysql_errno($this->conn) . ':' .
                         mysql_error($this->conn);

                trigger_error($error, E_USER_ERROR);

                return FALSE;

            }

        }

        return TRUE;

    }

    /**
     * Execute a SQL statement.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param sql A SQL statement.
     *
     * @return resource A result resource, if execution completes successfully,
     *                  otherwise <b>NULL</b>.
     *
     * @access public
     * @since  2.0
     */
    function & execute ($sql)
    {

        $result = @mysql_query($sql, $this->conn);

        if ($result !== FALSE)
        {

            return $result;

        }

        $error = 'Failed to execute SQL query: ' . $sql .
                 ' -- error: ' . mysql_errno($this->conn) . ':' .
                 mysql_error($this->conn);

        trigger_error($error, E_USER_ERROR);

	$null = NULL;
        return $null;
    }

    /**
     * Retrieve a row from a result set.
     *
     * @param resource A result set.
     *
     * @return array A database row.
     *
     * @access public
     * @since  2.0
     */
    function fetchRow (&$result)
    {

        return mysql_fetch_row($result);

    }

    /**
     * Retrieve the number of rows from a result set.
     *
     * @param resource A result set.
     *
     * @return int The number of rows in a result set.
     *
     * @access public
     * @since  2.0
     */
    function getNumRows (&$result)
    {

        return mysql_num_rows($result);

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

        if ($this->conn == NULL)
        {

            $function = ($this->persistent)
                        ? 'mysql_pconnect' : 'mysql_connect';

            if (isset($this->params['host']))
            {

                // host exists
                if (isset($this->params['user']))
                {

                    // user exists
                    if (isset($this->params['password']))
                    {

                        // password exists

                        // connect with host, user, password
                        $this->conn = $function($this->params['host'],
                                                 $this->params['user'],
                                                 $this->params['password']);

                    } else
                    {

                        // connect with host, user
                        $this->conn = $function($this->params['host'],
                                                 $this->params['user']);

                    }

                } else
                {

                    // connect with host
                    $this->conn = $function($this->params['host']);

                }

            } else
            {

                // connect with no params
                $this->conn = $function();

            }

            if ($this->conn !== FALSE)
            {

                if (!isset($this->params['db']))
                {

                    $error = 'Please specify a MySQL database';

                    trigger_error($error, E_USER_ERROR);

                    return FALSE;

                } else if (@mysql_select_db($this->params['db'], $this->conn) === FALSE)
                {

                    $error = 'Failed to select database -- reason: ' .
                             mysql_errno($this->conn) . ':' .
                             mysql_error($this->conn);

                    trigger_error($error, E_USER_ERROR);

                    return FALSE;

                }

            } else
            {

                $this->conn = NULL;

            }

        }

        return TRUE;

    }

}

?>
