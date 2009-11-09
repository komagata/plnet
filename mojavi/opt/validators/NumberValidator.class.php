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
 * NumberValidator verifies a parameter contains only numeric characters and can
 * be constrained with minimum and maximum values.
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
 *         <td valign="top">max</td>
 *         <td valign="top">int</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">no</td>
 *         <td valign="top">a maximum value</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">min</td>
 *         <td valign="top">int</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">no</td>
 *         <td valign="top">a minimum value</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">strip</td>
 *         <td valign="top">boolean</td>
 *         <td valign="top">true</td>
 *         <td valign="top">no</td>
 *         <td valign="top">strip non-numeric characters other than periods and hypens</td>
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
 *         <td valign="top">max_error</td>
 *         <td valign="top">Value is too high</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">min_error</td>
 *         <td valign="top">Value is too low</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">number_error</td>
 *         <td valign="top">Value is not numeric</td>
 *     </tr>
 * </table>
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package validators
 * @since   1.0
 */
class NumberValidator extends Validator
{

    /**
     * Create a new NumberValidator instance.
     *
     * @access public
     * @since  1.0
     */
    function NumberValidator ()
    {

        $this->params['max']          = -1;
        $this->params['max_error']    = 'Value is too high';
        $this->params['min']          = -1;
        $this->params['min_error']    = 'Value is too low';
        $this->params['number_error'] = 'Value is not numeric';
        $this->params['strip']        = TRUE;

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

        if ($this->params['strip'])
        {

            $value = preg_replace('/[^0-9\.\-]*/', '', $value);

        }

        if (!is_numeric($value))
        {

            $error = $this->params['number_error'];

            return FALSE;

        }

        if ($this->params['min'] > -1 && $value < $this->params['min'])
        {

            $error = $this->params['min_error'];

            return FALSE;

        }

        if ($this->params['max'] > -1 && $value > $this->params['max'])
        {

            $error = $this->params['max_error'];

            return FALSE;

        }

        return TRUE;

    }

}

?>