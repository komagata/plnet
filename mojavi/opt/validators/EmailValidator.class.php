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
 * EmailValidator verifies an email address has a correct format.
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
 *         <td valign="top">a maximum length</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">min</td>
 *         <td valign="top">int</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">no</td>
 *         <td valign="top">a minimum length</td>
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
 *         <td valign="top">email_error</td>
 *         <td valign="top">Invalid email address</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">max_error</td>
 *         <td valign="top">Email address is too long</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">min_error</td>
 *         <td valign="top">Email address is too short</td>
 *     </tr>
 * </table>
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package validators
 * @since   1.0
 */
class EmailValidator extends Validator
{

    /**
     * Create a new EmailValidator instance.
     *
     * @access public
     * @since 2.0
     */
    function EmailValidator ()
    {

        $this->params['email_error'] = 'Invalid email address';
        $this->params['max']         = -1;
        $this->params['max_error']   = 'Email address is too long';
        $this->params['min']         = -1;
        $this->params['min_error']   = 'Email address is too short';

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

        if (!preg_match('/^[a-z0-9\-\._]+@[a-z0-9]([0-9a-z\-]*[a-z0-9]\.){1,}' .
                        '[a-z]{1,4}$/i', $value))
        {

            $error = $this->params['email_error'];

            return FALSE;

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

        return TRUE;

    }

}

?>
