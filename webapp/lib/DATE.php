<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Rhett Waldock <rwaldock@gmail.com>                          |
// +----------------------------------------------------------------------+
//
// $Id: DATE.php,v 1.3 2006/03/18 01:19:30 aidan Exp $


/**
 * Replicate datetime constants
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/manual/en/ref.datetime.php
 * @author      Rhett Waldock <rwaldock@gmail.com>
 * @version     $Revision: 1.3 $
 * @since       PHP 5.1.1
 */
if (!defined('DATE_ATOM')) {
    define('DATE_ATOM', 'Y-m-d\TH:i:sO');
}

if (!defined('DATE_COOKIE')) {
    define('DATE_COOKIE', 'D, d M Y H:i:s T');
}

if (!defined('DATE_ISO8601')) {
    define('DATE_ISO8601', 'Y-m-d\TH:i:sO');
}

if (!defined('DATE_RFC822')) {
    define('DATE_RFC822', 'D, d M Y H:i:s T');
}

if (!defined('DATE_RFC850')) {
    define('DATE_RFC850', 'l, d-M-y H:i:s T');
}

if (!defined('DATE_RFC1036')) {
    define('DATE_RFC1036', 'l, d-M-y H:i:s T');
}

if (!defined('DATE_RFC1123')) {
    define('DATE_RFC1123', 'D, d M Y H:i:s T');
}

if (!defined('DATE_RFC2822')) {
    define('DATE_RFC2822', 'D, d M Y H:i:s O');
}

if (!defined('DATE_RSS')) {
    define('DATE_RSS', 'D, d M Y H:i:s T');
}

if (!defined('DATE_W3C')) {
    define('DATE_W3C', 'Y-m-d\TH:i:sO');
}
?>
