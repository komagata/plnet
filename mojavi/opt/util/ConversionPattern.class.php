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
 * @package util
 * @since   2.0
 */
class ConversionPattern
{

    /**
     * The function that will be called when a conversion character is parsed.
     *
     * @access private
     * @since  2.0
     * @type   string
     */
    var $func;

    /**
     * The object containing the function to be called when a conversion
     * character is parsed.
     *
     * @access private
     * @since  2.0
     * @type   object
     */
    var $obj;

    /**
     * A pattern containing conversion characters.
     *
     * @access private
     * @since  2.0
     * @type   string
     */
    var $pattern;

    /**
     * Create a new ConversionPattern instance.
     *
     * @param string A pattern containing conversion characters.
     *
     * @return ConversionPattern A ConversionPattern instance.
     *
     * @access public
     * @since  2.0
     */
    function ConversionPattern ($pattern = NULL)
    {

        $this->func    = NULL;
        $this->obj     = NULL;
        $this->pattern = $pattern;

    }

    /**
     * Retrieve a parameter for a conversion character.
     *
     * @param int The pattern index at which we're working.
     *
     * @return string A conversion character parameter if one one exists,
     *                otherwise <b>NULL</b>.
     *
     * @access private
     * @since  2.0
     */
    function getParameter (&$index)
    {

        $length = strlen($this->pattern);
        $param  = '';

        // skip ahead to parameter
        $index += 2;

        if ($index < $length)
        {

            // loop through conversion character parameter
            while ($this->pattern{$index} != '}' && $index < $length)
            {

                $param .= $this->pattern{$index};
                $index++;

            }

            if ($this->pattern{$index} == '}')
            {

                return $param;

            }

            // parameter found but no ending }

        }

        // oops, not enough text to go around
        return NULL;

    }

    /**
     * Retrieve the conversion pattern.
     *
     * @return string A conversion pattern.
     *
     * @access public
     * @since  2.0
     */
    function getPattern ()
    {

        return $this->pattern;

    }

    /**
     * Parse the conversion pattern.
     *
     * @return string A string with conversion characters replaced with their
     *                respective values.
     *
     * @access public
     * @since  2.0
     */
    function & parse ()
    {

        if ($this->pattern == NULL)
        {

            $error = 'A conversion pattern has not been specified';

            trigger_error($error, E_USER_ERROR);
	    $null = null;
            return $null;

        }

        $length  = strlen($this->pattern);
        $pattern = '';

        for ($i = 0; $i < $length; $i++)
        {

            if ($this->pattern{$i} == '%' &&
                ($i + 1) < $length)
            {

                if ($this->pattern{$i + 1} == '%')
                {

                    $data = '%';
                    $i++;

                } else
                {

                    // grab conversion char
                    $char  = $this->pattern{++$i};
                    $param = NULL;

                    // does a parameter exist?
                    if (($i + 1) < $length &&
                        $this->pattern{$i + 1} == '{')
                    {

                        // retrieve parameter
                        $param = $this->getParameter($i);

                    }

                    if ($this->obj == NULL)
                    {

                        $data = $function($char, $param);

                    } else
                    {

                        $object   =& $this->obj;
                        $function =& $this->func;

                        $data = $object->$function($char, $param);

                    }

                }

                $pattern .= $data;

            } else
            {

                $pattern .= $this->pattern{$i};

            }

        }

        return $pattern;

    }

    /**
     * Set the callback function.
     *
     * @param string A function name.
     *
     * @access public
     * @since  2.0
     */
    function setCallbackFunction ($function)
    {

        $this->func = $function;

    }

    /**
     * Set the callback object and function.
     *
     * @param object An object holding a callback function.
     * @param string A function name.
     *
     * @access public
     * @since  2.0
     */
    function setCallbackObject (&$object, $function)
    {

        $this->func =  $function;
        $this->obj  =& $object;

    }

    /**
     * Set the conversion pattern.
     *
     * @param string A pattern consisting of one or more conversion characters.
     *
     * @access public
     * @since  2.0
     */
    function setPattern ($pattern)
    {

        $this->pattern = $pattern;

    }

}

?>
