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
 * ErrorLogger provides a default logging mechanism for errors.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package logging
 * @since   2.0
 */
class ErrorLogger extends Logger
{

    /**
     * Create a new ErrorLogger instance.
     *
     * @access public
     * @since  2.0
     */
    function ErrorLogger ()
    {

        parent::Logger();

    }

    /**
     * Log a message with a debug priority.
     *
     * <br/><br/>
     *
     * <note>
     *     This has a priority level of 1000.
     * </note>
     *
     * @param string An error message.
     * @param string The class where message was logged.
     * @param string The function where message was logged.
     * @param string The file where message was logged.
     * @param int    The line where message was logged.
     *
     * @access public
     * @since  2.0
     */
    function debug ($message, $class = NULL, $function = NULL, $file = NULL,
                    $line = NULL)
    {

        $message =& new Message(array('m' => $message,
                                      'c' => $class,
                                      'F' => $function,
                                      'f' => $file,
                                      'l' => $line,
                                      'N' => 'DEBUG',
                                      'p' => LEVEL_DEBUG));

        $this->log($message);

    }

    /**
     * Log a message with an error priority.
     *
     * <br/><br/>
     *
     * <note>
     *     This has a priority level of 3000.
     * </note>
     *
     * @param string An error message.
     * @param string The class where message was logged.
     * @param string The function where message was logged.
     * @param string The file where message was logged.
     * @param int    The line where message was logged.
     *
     * @access public
     * @since  2.0
     */
    function error ($message, $class = NULL, $function = NULL, $file = NULL,
                    $line = NULL)
    {

        $message =& new Message(array('m' => $message,
                                      'c' => $class,
                                      'F' => $function,
                                      'f' => $file,
                                      'l' => $line,
                                      'N' => 'ERROR',
                                      'p' => LEVEL_ERROR));

        $this->log($message);

    }

    /**
     * Log a message with a fatal priority.
     *
     * <br/><br/>
     *
     * <note>
     *     This has a priority level of 5000.
     * </note>
     *
     * @param string An error message.
     * @param string The class where message was logged.
     * @param string The function where message was logged.
     * @param string The file where message was logged.
     * @param int    The line where message was logged.
     *
     * @access public
     * @since  2.0
     */
    function fatal ($message, $class = NULL, $function = NULL, $file = NULL,
                    $line = NULL)
    {

        $message =& new Message(array('m' => $message,
                                      'c' => $class,
                                      'F' => $function,
                                      'f' => $file,
                                      'l' => $line,
                                      'N' => 'FATAL',
                                      'p' => LEVEL_FATAL));

        $this->log($message);

    }

    /**
     * Log a message with a info priority.
     *
     * <br/><br/>
     *
     * <note>
     *     This has a priority level of 2000.
     * </note>
     *
     * @param string An error message.
     * @param string The class where message was logged.
     * @param string The function where message was logged.
     * @param string The file where message was logged.
     * @param int    The line where message was logged.
     *
     * @access public
     * @since  2.0
     */
    function info ($message, $class = NULL, $function = NULL, $file = NULL,
                   $line = NULL)
    {

        $message =& new Message(array('m' => $message,
                                      'c' => $class,
                                      'F' => $function,
                                      'f' => $file,
                                      'l' => $line,
                                      'N' => 'INFO',
                                      'p' => LEVEL_INFO));

        $this->log($message);

    }

    /**
     * Log an error handled by PHP.
     *
     * <br/><br/>
     *
     * <note>
     *     Do not call this method directly. Call the standard PHP function
     *     <i>trigger_error()</i>.
     * </note>
     *
     * @param int    A priority level.
     * @param string An error message.
     * @param string The file where the error occured.
     * @param int    The line where the error occured.
     *
     * @access public
     * @since  2.0
     */
    function standard ($level, $message, $file, $line)
    {

        // don't want to print supressed errors
        if (error_reporting() > 0)
        {

            switch ($level)
            {

				case E_NOTICE:
                case E_USER_NOTICE:

                    $this->info($message, NULL, NULL, $file, $line);
                    break;

				case E_WARNING;
				case E_CORE_WARNING:
				case E_COMPILE_WARNING:
                case E_USER_WARNING:

                    $this->warning($message, NULL, NULL, $file, $line);
                    break;

                default:

                    $this->fatal($message, NULL, NULL, $file, $line);

            }

        }

    }

    /**
     * Log a message with a warning priority.
     *
     * <br/><br/>
     *
     * <note>
     *     This has a priority level of 4000.
     * </note>
     *
     * @param string An error message.
     * @param string The class where message was logged.
     * @param string The function where message was logged.
     * @param string The file where message was logged.
     * @param int    The line where message was logged.
     *
     * @access public
     * @since  2.0
     */
    function warning ($message, $class = NULL, $function = NULL, $file = NULL,
                      $line = NULL)
    {

        $message =& new Message(array('m' => $message,
                                      'c' => $class,
                                      'F' => $function,
                                      'f' => $file,
                                      'l' => $line,
                                      'N' => 'WARNING',
                                      'p' => LEVEL_WARN));

        $this->log($message);

    }

}

?>
