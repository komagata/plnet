<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Mojavi default module.                           |
// | Copyright (c) 2003 Sean Kerr.                                             |
// |                                                                           |
// | For the full copyright and license information, please view the COPYRIGHT |
// | file that was distributed with this source code. If the COPYRIGHT file is |
// | missing, please visit the Mojavi homepage: http://www.mojavi.org          |
// +---------------------------------------------------------------------------+

/**
 * An absolute file-system path to the directory containing your static files.
 */
define('DEF_STATIC_DIR', BASE_DIR . 'static/');

/**
 * The default template to use when one isn't specified.
 */
define('DEF_STATIC_TEM', 'index.php');

/**
 * A single character or sequence of characters used to separate directories and
 * files.
 *
 * Notes:
 *
 *     This is to avoid conflicts with the path separator '/' when using
 *     PATH_FORMAT for URLs. If you're using GET_FORMAT, this can be a front
 *     slash (/).
 *
 *     This character or sequence of characters will be replaced with a front
 *     slash before the template is called.
 *
 *     This must be a character other than a front slash when using
 *     PATH_FORMAT for URLs.
 */
define('DEF_STATIC_SEP', ';');

/**
 * The request parameter name specifying the page to be included.
 */
define('DEF_STATIC_VAR', 'page');

/**
 * A list of regex patterns the requested template must match or not match in
 * order to validate successfully.
 *
 * Each pattern will be executed in the order it is added.
 *
 * Rules:
 *
 *     1. Each pattern is separated by a pound sign (#).
 *     2. An optional string (::(1|0)) is allowed to specify whether the pattern
 *        must match or must not match. The default is match.
 *
 *        Examples: /pattern/       must match
 *                  /pattern/::1    must match
 *                  /pattern/::0    must not match
 */
define('DEF_STATIC_PATS',

     '/^' . DEF_STATIC_SEP . '/::0#' . // cannot start with file separator
     '/\.\./::0#')                     // cannot contain a sequence of periods

?>