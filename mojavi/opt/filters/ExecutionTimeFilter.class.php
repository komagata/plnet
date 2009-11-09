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
 * ExecutionTimeFilter tracks the length of time (in seconds) that it takes for
 * an entire request to process.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package filters
 * @since   2.0
 */
class ExecutionTimeFilter extends Filter
{

    /**
     * Create a new ExecutionTimeFilter instance.
     *
     * @access public
     * @since  1.0
     */
    function ExecutionTimeFilter ()
    {

    }

    /**
     * Execute the filter.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param FilterChain A FilterChain instance.
     * @param Controller  A Controller instance.
     * @param Request     A Request instance.
     * @param User        A User instance.
     *
     * @access public
     * @since  1.0
     */
    function execute (&$filterChain, &$controller, &$request, &$user)
    {

        static $loaded;

        if ($loaded == NULL)
        {

            $loaded = TRUE;

            ob_start();

            $stimer = explode(' ', microtime());
            $stimer = $stimer[1] + $stimer[0];

            $filterChain->execute($controller, $request, $user);

            $etimer = explode(' ', microtime());
            $etimer = $etimer[1] + $etimer [0];
            $time   = round(($etimer - $stimer), 4);

            $content = str_replace('%EXEC_TIME%', $time, ob_get_contents());

            ob_clean();

            echo "$content\n<!-- Page was processed in $time seconds -->";

        } else
        {

            // filter has already been loaded
            $filterChain->execute($controller, $request, $user);

        }

    }

}

?>
