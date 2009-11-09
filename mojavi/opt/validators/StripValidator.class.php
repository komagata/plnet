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
 * StripValidator strips characters from a parameter.
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
 *         <td valign="top">chars</td>
 *         <td valign="top">array</td>
 *         <td valign="top">n/a</td>
 *         <td valign="top">yes</td>
 *         <td valign="top">an indexed array of characters to be stripped</td>
 *     </tr>
 * </table>
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package validators
 * @since   2.0
 */
class StripValidator extends Validator
{

    /**
     * Create a new StripValidator instance.
     *
     * @access public
     * @since  2.0
     */
    function StripValidator ()
    {

        $this->params['chars'] = array();

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
     * @since  2.0
     */
    function execute (&$value, &$error, &$controller, &$request, &$user)
    {

        $length = strlen($value);
        $newval = '';

        for ($i = 0; $i < $length; $i++)
        {

            if (!in_array($value{$i}, $this->params['chars']))
            {

                $newval .= $value{$i};

            }

        }

        $value = $newval;

        return TRUE;

    }

}

?>
