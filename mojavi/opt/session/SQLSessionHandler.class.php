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
require_once(SESSION_DIR . 'SessionHandler.class.php');
require_once(SQL_DIR . 'SQLStatement.class.php');

/**
 * SQLSessionHandler stores sessions in a database.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package session
 * @since   2.0
 */
class SQLSessionHandler extends SessionHandler
{

    /**
     * A database connection.
     *
     * @access protected
     * @since  2.0
     * @type   resource
     */
    var $conn;

    /**
     * A crc32 hash of the original session data.
     *
     * @access private
     * @since  2.0
     * @type   string
     */
    var $crc32Hash;

    /**
     * Whether or not to use a crc32 hash to check for a modified
     * session.
     *
     * @access private
     * @since  2.0
     * @type   bool
     */
    var $crc32Check;

    /**
     * Whether or not the session already exists.
     *
     * @access private
     * @since  2.0
     * @type   bool
     */
    var $exists;

    /**
     * A SQLStatement instance.
     *
     * @access private
     * @since  2.0
     * @type   SQLStatement
     */
    var $stat;

    /**
     * Create a new SQLSessionHandler instance.
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
     * @param array An associative array of database parameters.
     *
     * @access protected
     * @since  2.0
     */
    function SQLSessionHandler ($params)
    {

        $this->conn       =  NULL;
        $this->crc32Hash  =  NULL;
        $this->crc32Check =  FALSE;
        $this->exists     =  FALSE;
        $this->stat       =& new SQLStatement;

        // should we do a crc32 check?
        if ($params != NULL && isset($params['crc32_check']))
        {

            $this->crc32Check = $params['crc32_check'];

        }

        // set default parameters
        $defparams             = array();
        $defparams['table']    = 'sessions';
        $defparams['data_col'] = 'data';
        $defparams['id_col']   = 'session_id';
        $defparams['ts_col']   = 'access_date';

        // merge parameters
        $params = array_merge($defparams, $params);

        // set database attributes
        $this->stat->setRawAttribute('table',    $params['table']);
        $this->stat->setRawAttribute('data_col', $params['data_col']);
        $this->stat->setRawAttribute('id_col',   $params['id_col']);
        $this->stat->setRawAttribute('ts_col',   $params['ts_col']);

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

        if ($this->conn != NULL)
        {

            $sql = 'DELETE FROM %a{table} WHERE %a{id_col} = ?';

            $this->stat->setStringValue(1, $id);

            $sql = $this->stat->prepare($sql);

            if ($this->execute($sql) != NULL)
            {

                return TRUE;

            }

        }

        return FALSE;

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

        if ($this->conn != NULL)
        {

            $sql = 'DELETE FROM %a{table} WHERE %a{ts_col} < ?';

            $this->stat->setRawValue(1, (time() - $lifetime));

            $sql = $this->stat->prepare($sql);

            if ($this->execute($sql) != NULL)
            {

                return TRUE;

            }

        }

        return FALSE;

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

        $sql = 'SELECT %a{data_col} FROM %a{table} WHERE %a{id_col} = ?';

        $this->stat->setStringValue(1, $id);

        $sql    = $this->stat->prepare($sql);
        $result = $this->execute($sql);

        if ($result == NULL)
        {

            return FALSE;

        }

        if ($this->getNumRows($result) > 0)
        {

            $record = $this->fetchRow($result);

            if ($this->crc32Check)
            {

                // generate crc32 for data
                $this->crc32Hash = strlen($record[0]) . crc32($record[0]);

            }

            $this->exists = TRUE;

            return $record[0];

        }

        return '';

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
     * @param data A session data.
     *
     * @access public
     * @since  2.0
     */
    function write ($id, &$data)
    {

        // does the session already exist?
        if ($this->exists)
        {

            // session already exists
            if ($this->crc32Check)
            {

                $crc32Hash = strlen($data) . crc32($data);

                if ($this->crc32Hash == $crc32Hash)
                {

                    // just update timestamp
                    $sql = 'UPDATE %a{table} SET %a{ts_col} = ?
                            WHERE %a{id_col} = ?';

                    $this->stat->setRawValue(1, time());
                    $this->stat->setStringValue(2, $id);

                }

            }

            if (!isset($sql))
            {

                // update all data
                $sql = 'UPDATE %a{table} SET %a{data_col} = ?,
                        %a{ts_col} = ? WHERE %a{id_col} = ?';

                $this->stat->setStringValue(1, $data);
                $this->stat->setRawValue(2, time());
                $this->stat->setStringValue(3, $id);

            }

            $sql = $this->stat->prepare($sql);

        } else
        {

            // create new session
            $sql = 'INSERT INTO %a{table}
                    (%a{id_col}, %a{data_col}, %a{ts_col})
                    VALUES
                    (?, ?, ?)';

            $this->stat->setStringValue(1, $id);
            $this->stat->setStringValue(2, $data);
            $this->stat->setRawValue(3, time());

            $sql = $this->stat->prepare($sql);

        }

        return ($this->execute($sql) != NULL) ? TRUE : FALSE;

    }

    /**
     * Retrieve the database connection.
     *
     * @return resource A database connection.
     *
     * @access public
     * @since  2.0
     */
    function & getConnection ()
    {

        return $this->conn;

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

        $error = 'SQLSessionHandler::getNumRows(&$result) must be overridden';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

    /**
     * Set the database connection.
     *
     * @param resource A database connection.
     *
     * @access public
     * @since  2.0
     */
    function setConnection (&$conn)
    {

        $this->conn =& $conn;

    }

}

?>
