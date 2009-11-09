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
 * ChoiceValidator provides a constraint on a parameter by making sure the value
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
 *         <td valign="top">match</td>
 *         <td valign="top">bool</td>
 *         <td valign="top"><b>TRUE</b></td>
 *         <td valign="top">no</td>
 *         <td valign="top">whether or not the pattern must match or must not
 *         match</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">pattern</td>
 *         <td valign="top">string</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">yes</td>
 *         <td valign="top">a regular expression pattern</td>
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
 *         <td valign="top">pattern_error</td>
 *         <td valign="top">Pattern does not match</td>
 *     </tr>
 * </table>
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package validators
 * @since   1.0
 */
class RegexValidator extends Validator
{

    /**
     * Create a new RegexValidator instance.
     *
     * @access public
     * @since  1.0
     */
    function RegexValidator ()
    {

        $this->params['match']         = TRUE;
        $this->params['pattern_error'] = 'Pattern does not match';

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

        $match = preg_match($this->params['pattern'], $value);

        if ($this->params['match'] && !$match)
        {

            // pattern doesn't match
            $error = $this->params['pattern_error'];

            return FALSE;

        } else if (!$this->params['match'] && $match)
        {

            // pattern matches
            $error = $this->params['pattern_error'];

            return FALSE;

        }

        return TRUE;

    }

}

?>