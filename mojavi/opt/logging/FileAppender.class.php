<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Mojavi package.                                  |
// | Copyright (c) 2003 Sean Kerr.                                             |
// |                                                                           |
// | For the full copyright and license information, please view the COPYRIGHT |
// | file that was distributed with this source code. If the COPYRIGHT file is |
// | missing, please visit the Mojavi homepage: http://www.mojavi.org          |
// +---------------------------------------------------------------------------+

// include dependencies
require_once(UTIL_DIR . 'ConversionPattern.class.php');

/**
 * FileAppender allows a logger to write a message to file.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package logging
 * @since   2.0
 */
class FileAppender extends Appender
{

    /**
     * Whether or not the file pointer is opened in append mode.
     *
     * @access private
     * @since  2.0
     * @type   bool
     */
    var $append;

    /**
     * An absolute file-system path to the log file.
     *
     * @access private
     * @since  2.0
     * @type   string
     */
    var $file;

    /**
     * A pointer to the log file.
     *
     * @access private
     * @since  2.0
     * @type   resource
     */
    var $fp;

    /**
     * The conversion pattern to use with this layout.
     *
     * @access private
     * @since  2.0
     * @type   ConversionPattern
     */
    var $pattern;

    /**
     * Create a new FileAppender instance.
     *
     * <br/><br/>
     *
     * Conversion characters:
     *
     * <ul>
     *     <li><b>%C{constant}</b> - the value of a PHP constant</li>
     *     <li><b>%d{format}</b>   - a date (uses date() format)</li>
     * </ul>
     *
     * @param Layout A Layout instance.
     * @param string An absolute file-system path to the log file.
     * @param bool   Whether or not the file pointer should be opened in
     *               appending mode (if false, all data is truncated).
     *
     * @access public
     * @since  2.0
     */
    function FileAppender ($layout, $file, $append = TRUE)
    {

        parent::Appender($layout);

        $this->append  =  $append;
        $this->file    =  $file;
        $this->pattern =& new ConversionPattern($file);

        $this->openFP();

    }

    /**
     * ConversionPattern callback method.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param string A conversion character.
     * @param string A conversion parameter.
     *
     * @return string A replacement for the given data.
     *
     * @access public
     * @since  2.0
     */
    function & callback ($char, $param)
    {

        switch ($char)
        {

            case 'C':

                $data = (defined($param)) ? constant($param) : '';

                break;

            case 'd':

                // get the date
                if ($param == NULL)
                {

                    $param = 'd_j_y';

                }

                $data = date($param);

        }

        return $data;

    }

    /**
     * Close the file pointer.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @access public
     * @since  2.0
     */
    function cleanup ()
    {

        if ($this->fp != NULL)
        {

            fflush($this->fp);
            fclose($this->fp);

            $this->fp = NULL;

        }

    }

    /**
     * Open the file pointer.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @access private
     * @since  2.0
     */
    function openFP ()
    {

        // register callback method
        // this cannot be done in the constructor
        $this->pattern->setCallbackObject($this, 'callback');

        $this->file = $this->pattern->parse();
        @chmod($this->file, 0777);
        $this->fp   = @fopen($this->file, ($this->append) ? 'a' : 'w');

        if ($this->fp === FALSE)
        {
            $error = 'Failed to open log file ' . $this->file . ' for writing';

            trigger_error($error, E_USER_ERROR);

        }
    }

    /**
     * Write a message to the log file.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param string The message to write.
     *
     * @access public
     * @since  2.0
     */
    function write ($message)
    {

        fputs($this->fp, $message);
        fflush($this->fp);
        @chmod($this->file, 0777);
    }

}

?>
