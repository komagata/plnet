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
 * StringValidator provides a constraint on a parameter by making sure the value
 * is or is not allowed in a list of choices.
 *
 * <br/><br/>
 *
 * Initialization Parameters:
 *
 * <br/><br/>
 *
 * <table border="0" cellpadding="3" cellspacing="0">
 *     <tr>
 *         <th>Name</th>
 *         <th>Type</th>
 *         <th>Default</th>
 *         <th>Required</th>
 *         <th>Description</th>
 *     </tr>
 *     <tr>
 *         <td valign="top">allowed</td>
 *         <td valign="top">bool</td>
 *         <td valign="top"><b>FALSE</b></td>
 *         <td valign="top">yes</td>
 *         <td valign="top">whether or not the list of characters contains valid
 *         or invalid values</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">chars</td>
 *         <td valign="top">array</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">yes</td>
 *         <td valign="top">an indexed array of characters</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">max</td>
 *         <td valign="top">int</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">no</td>
 *         <td valign="top">a maximum length</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">min</td>
 *         <td valign="top">int</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">no</td>
 *         <td valign="top">a minimum length</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">trim</td>
 *         <td valign="top">bool</td>
 *         <td valign="top"><b>TRUE</b></td>
 *         <td valign="top">no</td>
 *         <td valign="top">whether or not to trim the value before
 *         comparison</td>
 *     </tr>
 * </table>
 *
 * <br/><br/>
 *
 * Error Messages:
 *
 * <br/><br/>
 *
 * <table border="0" cellpadding="3" cellspacing="0">
 *     <tr>
 *         <th>Name</th>
 *         <th>Default</th>
 *     </tr>
 *     <tr>
 *         <td valign="top">chars_error</td>
 *         <td valign="top">Value contains an invalid character</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">max_error</td>
 *         <td valign="top">Value is too long</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">min_error</td>
 *         <td valign="top">Value is too short</td>
 *     </tr>
 * </table>
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package validators
 * @since   1.0
 */
class StringValidator extends Validator
{

    /**
     * Create a new StringValidator instance.
     *
     * @access public
     * @since  1.0
     */
    function StringValidator ()
    {

        $this->params['allowed']     = FALSE;
        $this->params['chars']       = array();
        $this->params['chars_error'] = 'Value contains an invalid character';
        $this->params['max']         = -1;
        $this->params['max_error']   = 'Value is too long';
        $this->params['min']         = -1;
        $this->params['min_error']   = 'Value is too short';
        $this->params['trim']        = TRUE;

    }

    /**
     * Execute this validator.
     *
     * @param string     A user submitted parameter value.
     * @param string     The error message variable to be set, if an error
     *                   occurs.
     * @param Controller A Controller instance.
     * @param Request    A Request instance.
     * @param User       A User instance.
     *
     * @return bool <b>TRUE</b>, if the validator completes successfully,
     *              otherwise <b>FALSE</b>.
     *
     * @access public
     * @since  1.0
     */
    function execute (&$value, &$error, &$controller, &$request, &$user)
    {

        $count = sizeof($this->params['chars']);

        if ($this->params['trim'])
        {

            $value = trim($value);

        }

        $length = strlen($value);

        if ($this->params['min'] > -1 && $length < $this->params['min'])
        {

            $error = $this->params['min_error'];

            return FALSE;

        }

        if ($this->params['max'] > -1 && $length > $this->params['max'])
        {

            $error = $this->params['max_error'];

            return FALSE;

        }

        if ($count > 0)
        {

            for ($i = 0; $i < $length; $i++)
            {

                $found = FALSE;

                for ($x = 0; $x < $count; $x++)
                {

                    if ($value[$i] == $this->params['chars'][$x])
                    {

                        $found = TRUE;

                        break;

                    }

                }

                if (($this->params['allowed'] && !$found) ||
                    (!$this->params['allowed'] && $found))
                {


                    $error = $this->params['chars_error'];

                    return FALSE;

                }

            }

        }

        return TRUE;

    }

}

?>