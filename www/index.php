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
 * INCLUDE config.php
 */
require_once(dirname(dirname(__FILE__))."/webapp/config.php");

/**
 * INCLUDE CORE FILES AND CREATE CONTROLLER
 *
 * All core Mojavi files are included here.
 *
 * Note: This section of initialization must be performed after inclusion of the
 *       configuration file.
 */
require_once(MOJAVI_FILE);

$controller =& Controller::getInstance();

// include after MOJAVI_FILE
require_once 'RESTAction.class.php';

/**
 * SECURITY SETTINGS
 *
 * By default, a PrivilegeAuthorizationHandler is used. It requires the
 * PrivilegeUser class. If you wish to provide custom authorization, you'll need
 * to create a custom AuthorizationHandler and User.
 *
 * The default security system checks only for user privileges. It is possible
 * to extend User and check for Roles or whatever other method you have in mind.
 * Please view the opt/auth/PrivilegeAuthorizationHandler and
 * opt/user/PrivilegeUser classes for examples.
 *
 * It's also possible to not use any security. Simply comment out the following
 * security related code and you'll have a user with no security related data.
 */
//require_once(AUTH_DIR . 'PrivilegeAuthorizationHandler.class.php');
require_once(AUTH_DIR . 'PrivilegeAuthorizationRedirectHandler.class.php');
require_once(USER_DIR . 'PrivilegeUser.class.php');

//$authHandler =& new PrivilegeAuthorizationHandler;
$authHandler =& new PrivilegeAuthorizationRedirectHandler;
$user        =& new PrivilegeUser;
$controller->setAuthorizationHandler($authHandler);
$controller->setUser($user);

/**
 * LOG SETTINGS
 *
 * By default, a logger by the name of 'default' exists, which appends to
 * stdout. You can add an additional appenders and/or remove the stdout
 * appender.
 *
 * Feel free to register other loggers as well. It's quite nice to have an
 * additional event logger too, which uses custom log level's that describe
 * system events.
 *
 * Examples are provided below:
 *
 * --- retrieve the default logger (this cannot be removed from LogManager)
 * $deflog =& LogManager::getLogger();
 *
 * --- log a warning
 * --- only the message parameter is required for logging to the default logger
 * $deflog->warning('This is a warning', 'MyClass', 'MyFunction', 'MyFile',
 *                  'MyLine');
 *
 * --- add an additional appender which will log to a file parallel with stdout
 *
 * --- use a PatternLayout to format the message
 * $layout =& new PatternLayout('%N [%c:%F:%l] %m');
 *
 * --- use a FileAppender to log to file using the pattern above
 * --- write to log file LOG_DIR/mojavi_<date>.log
 * --- FileAppender takes a %d date conversion character so you can write to
 * --- dated log files
 * require_once(LOGGING_DIR . 'FileAppender.class.php');
 * $appender =& new FileAppender($layout, LOG_DIR . 'mojavi_%d{n_j_y}.log');
 * $deflog->addAppender('file', $appender);
 */
include_once BASE_DIR . '/lib/LogUtils.php';

/**
 * USER CONTAINER SETTINGS
 *
 * By default, when sessions are enabled, a SessionContainer is used. When
 * sessions are disabled an ArrayContainer is used. You only need to register
 * a user container when you wish to provide a custom way of handling user
 * persistence. More than likely, you will never have to register one.
 *
 * Examples are provided below:
 *
 * --- use a MyCustomContainer instead of SessionContainer
 *
 * require_once('MyCustomContainer.class.php');
 * $userContainer =& new MyCustomContainer;
 * $user          =& $controller->getUser();
 * $user->setContainer($userContainer);
 */


/**
 * SESSION SETTINGS
 *
 * By default, no session handler is used. You only need to register one if you
 * wish to provide custom session storage.
 *
 * Examples are provided below:
 *
 * --- we'll use a PostgreSQL session handler so we can store sessions in a
 * --- database
 * require_once(SESSION_DIR . 'PgSQLSessionHandler.class.php');
 * $sessHandler =& new PgSQLSessionHandler('user=USER password=PASS dbname=DB');
 *
 * --- register our custom session handler with the controller
 * $controller->setSessionHandler($sessHandler);
 */




/**
 * Remove this comment and the following die() statement once you have fully
 * configured your Mojavi installation.
 */
//die('Please configure your Mojavi installation and remove this line from index.php.');


/**
 * DISPATCH REQUEST
 *
 * This tells the controller to handle the request.
 *
 * Note: This section of initialization must be performed last.
 */
$controller->dispatch();
?>
