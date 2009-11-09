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
 * PgSQLSessionHandler stores sessions in a PostgreSQL database.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package session
 * @since   2.0
 */
class PgSQLSessionHandler extends SQLSessionHandler
{

    /**
     * A PostgreSQL connection string.
     *
     * @access private
     * @since  2.0
     * @type   string
     */
    var $dbstring;

    /**
     * Whether or not the connection must be persistent.
     *
     * @access private
     * @since  2.0
     * @type   bool
     */
    var $persistent;

    /**
     * Create a new PgSQLSessionHandler instance.
     *
     * <br/><br/>
     *
     * <b>Available parameters:</b>
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
     * @param string A connection string.
     * @param array  An associative array of database parameters.
     * @param bool   Whether or not the connection must be persistent.
     */
    function PgSQLSessionHandler ($dbstring = NULL, $params = NULL,
                                    $persistent = FALSE)
    {

        parent::SQLSessionHandler($params);

        $this->dbstring   = $dbstring;
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

            if (@pg_close($this->conn) === FALSE)
            {

                $error = 'Failed to close the PostgreSQL session -- error: ' .
                         pg_last_error($this->conn);

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

        $result = @pg_exec($this->conn, $sql);

        if ($result !== FALSE)
        {

            return $result;

        }

        $error = 'Failed to execute SQL query: ' . $sql .
                 ' -- error: ' . pg_last_error($this->conn);

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

        return pg_fetch_row($result);

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

        return pg_num_rows($result);

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

            if ($this->dbstring == NULL)
            {

                $error = 'Please specify a PostgreSQL connection string';

                trigger_error($error, E_USER_ERROR);

                return FALSE;

            }

            $this->conn = ($this->persistent)
                           ? pg_pconnect($this->dbstring)
                           : pg_connect($this->dbstring);

            if ($this->conn === FALSE)
            {

                $this->conn = NULL;

                return FALSE;

            }

        }

        return TRUE;

    }

}

?>
