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
 * StdoutAppender logs a message directly to the requesting client.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package logging
 * @since   2.0
 */
class StdoutAppender extends Appender
{

    /**
     * Create a new FileAppender instance.
     *
     * @param Layout A Layout instance.
     *
     * @access public
     * @since  2.0
     */
    function StdoutAppender ($layout)
    {

        parent::Appender($layout);

    }

    /**
     * Write a message directly to the requesting client.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param string The message to be written.
     *
     * @access public
     * @since  2.0
     */
    function write ($message)
    {

        echo $message;

    }

}

?>
