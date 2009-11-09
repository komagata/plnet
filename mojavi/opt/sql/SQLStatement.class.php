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
require_once(UTIL_DIR . 'ConversionPattern.class.php');

/**
 * SQLStatement provides a simple method of auto-generating SQL statements.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package sql
 * @since   2.0
 */
class SQLStatement
{

    /**
     * An associative array of attributes and their associated values.
     *
     * @access private
     * @since  2.0
     * @type   array
     */
    var $attributes;

    /**
     * Whether or not any attribute value has been modified.
     *
     * @access private
     * @since  2.0
     * @type   bool
     */
    var $modified;

    /**
     * A ConversionPattern instance.
     *
     * @access private
     * @since  2.0
     * @type   ConversionPattern
     */
    var $pattern;

    /**
     * An indexed array of preparable values.
     *
     * @access private
     * @since  2.0
     * @type   array
     */
    var $values;

    /**
     * A preparable SQL statement.
     *
     * @access private
     * @since  2.0
     * @type   string
     */
    var $stat;

    /**
     * Create a new SQLStatement instance.
     *
     * @param array An indexed array of default attribute names.
     *
     * @access public
     * @since  2.0
     */
    function SQLStatement ($attributes = NULL)
    {

        $this->attributes =  array();
        $this->modified   =  FALSE;
        $this->pattern    =& new ConversionPattern;
        $this->values     =  array();
        $this->stat       =  NULL;

        if ($attributes != NULL)
        {

            // load default attribute set
            $count = sizeof($attributes);

            for ($i = 0; $i < $count; $i++)
            {

                $this->attributes[$attributes[$i]] = NULL;

            }

        }

    }

    /**
     * ConversionPattern callback method.
     *
     * @param string A conversion character.
     * @param string A conversion parameter.
     *
     * @return string A replacement for the given character and parameter.
     *
     * @access public
     * @since  2.0
     */
    function & callback ($char, $param)
    {

        $data = '';

        switch ($char)
        {

            case 'e':

                // attribute = value set
                $keys  = array_keys($this->attributes);
                $count = sizeof($keys);

                for ($i = 0; $i < $count; $i++)
                {

                    if ($i > 0)
                    {

                        $data .= ', ';

                    }

                    $data .= $keys[$i] . ' = ' . $this->attributes[$keys[$i]];

                }

                break;

            case 'n':

                $data = "\n";
                break;

            case 't':

                $data = "\t";
                break;

            case 'T':

                $data = time();
                break;

            // conversion chars with a parameter
            case 'a':

                // get an attribute
                if (isset($this->attributes[$param]))
                {

                    $data =& $this->attributes[$param];

                }

                break;

            case 'A':

                if ($param == 'names')
                {

                    // attribute names list
                    $data = implode(', ', array_keys($this->attributes));

                } else if ($param == 'values')
                {

                    // attributes value list
                    $data = implode(', ', array_values($this->attributes));

                }

                break;

            case 'C':

                // constant
                if (defined($param))
                {

                    $data = constant($param);

                }

                break;

            case 'v':

                // get an indexed value
                $param = (int) $param;

                // user indices start at 1 so we need to subtract
                $param--;

                if (isset($this->values[$param]))
                {

                    $data =& $this->values[$param];

                }

                break;

            case 'V':

                // values list
                $data = implode(', ', array_values($this->values));

        }

        return $data;

    }

    /**
     * Clear all attributes and values.
     *
     * @access public
     * @since  2.0
     */
    function clearAll ()
    {

        $this->clearAttributes();
        $this->clearValues();

    }

    /**
     * Clear all attributes.
     *
     * @access public
     * @since  2.0
     */
    function clearAttributes ()
    {

        $this->attributes = NULL;
        $this->attributes = array();
        $this->modified   = FALSE;

    }

    /**
     * Clear all values.
     *
     * @access public
     * @since  2.0
     */
    function clearValues ()
    {

        $this->values = NULL;
        $this->values = array();

    }

    /**
     * Retrieve the original SQL statement.
     *
     * @return string A SQL statement.
     *
     * @access public
     * @since  2.0
     */
    function getStatement ()
    {

        return $this->stat;

    }

    /**
     * Determine whether or not any attribute value has been modified.
     *
     * @return bool <b>TRUE</b>, if any attribute value has been modified,
     *              otherwise <b>FALSE</b>.
     *
     * @access public
     * @since  2.0
     */
    function isModified ()
    {

        return $this->modified;

    }

    /**
     * Prepare the SQL statement.
     *
     * <br/><br/>
     *
     * <note>
     *     Preparable values are replaced before conversion characters.
     * </note>
     *
     * <br/><br/>
     *
     * <b>Conversion characters:</b>
     *
     * <ul>
     *     <li><b>%a{attribute}</b> - the value of an attribute</li>
     *     <li><b>%A{names|values}</b> - a comma separated list of attribute
     *         data</li>
     *     <li><b>%C{constant}</b> - the value of a PHP constant</li>
     *     <li><b>%e</b> - a comma separated list of name = value pairs
     *         (UPDATE format)</li>
     *     <li><b>%n</b> - a newline</li>
     *     <li><b>%t</b> - a horizontal tab</li>
     *     <li><b>%T</b> - a unix timestamp</li>
     *     <li><b>%v{index}</b> - the value at a given index</li>
     *     <li><b>%V</b> - a comma separated list of values</li>
     * </ul>
     *
     * @param string A SQL statement.
     *
     * @return string A prepared SQL statement.
     *
     * @access public
     * @since  2.0
     */
    function & prepare ($statement)
    {

        $this->stat = $statement;

        if (strpos($statement, '?') !== FALSE)
        {

            // replace prepareable values
            $count        = strlen($this->stat);
            $index        = -1;
            $oldStatement = $this->stat;
            $statement    = '';

            // loop through statement
            for ($i = 0; $i < $count; $i++)
            {

                if ($oldStatement{$i} == '?')
                {

                    $index++;

                    if (isset($this->values[$index]))
                    {

                        $statement .= $this->values[$index];

                    } else
                    {

                        $error = 'SQL statement does not contain a value for ' .
                                 'preparable index #' . ($index + 1);

                        trigger_error($error, E_USER_ERROR);

                    }

                } else
                {

                    $statement .= $oldStatement{$i};

                }

            }

        }

        if (sizeof($this->attributes) > 0)
        {

            // replace conversion character values
            // cannot set the call back object in this constructor,
            // so it must be set here
            $this->pattern->setCallbackObject($this, 'callback');
            $this->pattern->setPattern($statement);

            $statement =& $this->pattern->parse();

        }

        return $statement;

    }

    /**
     * Remove an attribute.
     *
     * @param string An attribute name.
     *
     * @access public
     * @since  2.0
     */
    function removeAttribute ($name)
    {

        if (isset($this->attributes[$name]))
        {

            unset($this->attributes[$name]);

        }

    }

    /**
     * Set an attribute that does not require modification.
     *
     * @param string An attribute name.
     * @param mixed  An attribute value.
     *
     * @access public
     * @since  2.0
     */
    function setRawAttribute ($name, $value)
    {

        $this->modified          = TRUE;
        $this->attributes[$name] = $value;

    }

    /**
     * Set an attribute as a quoted and escaped string.
     *
     * <br/><br/>
     *
     * <note>
     *     There is no setStringAttributeByRef() because this method alters
     *     $value.
     * </note>
     *
     * @param string An attribute name.
     * @param mixed  An attribute value.
     *
     * @access public
     * @since  2.0
     */
    function setStringAttribute ($name, $value)
    {

        $this->modified          = TRUE;
        $value                   = addslashes($value);
        $this->attributes[$name] = "'" . $value . "'";

    }

    /**
     * Set a preparable value that does not modification.
     *
     * @param string A value index.
     * @param mixed  A value.
     *
     * @access public
     * @since  2.0
     */
    function setRawValue ($index, $value)
    {

        if ($index > 0)
        {

            $this->values[--$index] = $value;

            return;

        }

        $error = 'SQL statement value index #' . $index . ' is invalid';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

    /**
     * Set a preparable value as a quoted and escaped string.
     *
     * @param string A value index.
     * @param mixed  A value.
     *
     * @access public
     * @since  2.0
     */
    function setStringValue ($index, $value)
    {

        if ($index > 0)
        {

            $value                  = addslashes($value);
            $this->values[--$index] = "'" . $value . "'";

            return;

        }

        $error = 'SQL statement value index #' . $index . ' is invalid';

        trigger_error($error, E_USER_ERROR);

        exit;

    }

}

?>
