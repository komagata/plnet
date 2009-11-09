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
 *         <td valign="top">choices</td>
 *         <td valign="top">array</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">yes</td>
 *         <td valign="top">an indexed array choices</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">sensitive</td>
 *         <td valign="top">bool</td>
 *         <td valign="top"><b>FALSE</b></td>
 *         <td valign="top">no</td>
 *         <td valign="top">whether or not the choices are case-sensitive</td>
 *     </tr>
 *     <tr>
 *         <td valign="top">valid</td>
 *         <td valign="top">bool</td>
 *         <td valign="top"><b>TRUE</b></td>
 *         <td valign="top">no</td>
 *         <td valign="top">whether or not list of choices contains valid or
 *         invalid values</td>
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
 *         <td valign="top">choices_error</td>
 *         <td valign="top">Invalid value</td>
 *     </tr>
 * </table>
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package validators
 * @since   1.0
 */
class ChoiceValidator extends Validator
{

    /**
     * Create a new ChoiceValidator instance.
     *
     * @access public
     * @since  1.0
     */
    function ChoiceValidator ()
    {

        parent::Validator();

        $this->params['choices']       = array();
        $this->params['choices_error'] = 'Invalid value';
        $this->params['sensitive']     = FALSE;
        $this->params['valid']         = TRUE;

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

        $found = FALSE;

        if (!$this->params['sensitive'])
        {

            $newValue = strtolower($value);

        } else
        {

            $newValue =& $value;

        }

        // is the value in our choices list?
        if (in_array($newValue, $this->params['choices']))
        {

            $found = TRUE;

        }

        if (($this->params['valid'] && !$found) ||
            (!$this->params['valid'] && $found))
        {

            $error = $this->params['choices_error'];

            return FALSE;

        }

        return TRUE;

    }

    /**
     * Initialize the validator.
     *
     * @param array An associative array of initialization parameters.
     *
     * @access public
     * @since  1.0
     */
    function initialize ($params)
    {

        parent::initialize($params);

        if ($this->params['sensitive'] == FALSE)
        {

            // strtolower all choices
            $count = sizeof($this->params['choices']);

            for ($i = 0; $i < $count; $i++)
            {

                $this->params['choices'][$i] = strtolower($this->params['choices'][$i]);

            }


        }

    }

}

?>