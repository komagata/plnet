<?php
//
// MojaviInfo - Program that displays information on Mojavi2.
//
// Copyright (C) 2005 Masaki Komagata <komagata@p0t.jp> 
//     All rights reserved.
//     This is free software with ABSOLUTELY NO WARRANTY.
//
// You can redistribute it and/or modify it under the terms of 
// the GNU General Public License version 2.
//
define('MOJAVIINFO_VERSION', '0.4.1');

class IndexAction extends Action
{
	function execute(&$controller, &$request, &$user)
    {
		$modules = array();
		$configs = array();
		$loggers = array();
		$defines = array();
		foreach (get_defined_constants() as $key => $value) $defines[$value] = $key;

		// Configuration
		$configs['BASE_DIR'] = BASE_DIR;
		$configs['LOG_DIR'] = LOG_DIR;
		$configs['MOJAVI_FILE'] = MOJAVI_FILE;
		$configs['WEB_MODULE_DIR'] = WEB_MODULE_DIR;
		$configs['SCRIPT_PATH'] = SCRIPT_PATH;
		$configs['MODULE_ACCESSOR'] = MODULE_ACCESSOR;
		$configs['ACTION_ACCESSOR'] = ACTION_ACCESSOR;
		$configs['AUTH_MODULE'] = AUTH_MODULE;
		$configs['AUTH_ACTION'] = AUTH_ACTION;
		$configs['DEFAULT_MODULE'] = DEFAULT_MODULE;
		$configs['DEFAULT_ACTION'] = DEFAULT_ACTION;
		$configs['ERROR_404_MODULE'] = ERROR_404_MODULE;
		$configs['ERROR_404_ACTION'] = ERROR_404_ACTION;
		$configs['SECURE_MODULE'] = SECURE_MODULE;
		$configs['SECURE_ACTION'] = SECURE_ACTION;
		$configs['UNAVAILABLE_MODULE'] = UNAVAILABLE_MODULE;
		$configs['UNAVAILABLE_ACTION'] = UNAVAILABLE_ACTION;
		$configs['AVAILABLE'] = AVAILABLE;
		$configs['DISPLAY_ERRORS'] = DISPLAY_ERRORS;
		$configs['PATH_INFO_ARRAY'] = PATH_INFO_ARRAY;
		$configs['PATH_INFO_KEY'] = PATH_INFO_KEY;
		$configs['URL_FORMAT'] = URL_FORMAT;
		$configs['USE_SESSIONS'] = USE_SESSIONS;
		$configs['DISPLAY_ERRORS'] = DISPLAY_ERRORS;
		$configs['include_path'] = join("<br />\n", split(PATH_SEPARATOR, ini_get('include_path')));
		$request->setAttribute("configs", $configs);
		
		// Modules
		foreach (IndexAction::getFilesByDir(MODULE_DIR) as $moduleName) {
			$actions = is_readable(MODULE_DIR."/".$moduleName."/actions") ? IndexAction::getFilesByDir(MODULE_DIR."/".$moduleName."/actions") : array();
			$views = is_readable(MODULE_DIR."/".$moduleName."/views") ? IndexAction::getFilesByDir(MODULE_DIR."/".$moduleName."/views") : array();
			$templates = is_readable(MODULE_DIR."/".$moduleName."/templates") ? IndexAction::getFilesByDir(MODULE_DIR."/".$moduleName."/templates") : array();
			
			$modules[$moduleName] = array(
				"actions" => $actions, 
				"views" => $views, 
				"templates" => $templates
			);
		}
		$request->setAttribute("modules", $modules);
		
		// Global Filter List
		$gf = new GlobalFilterList();
		$request->setAttribute("globalFilterList", array_keys($gf->filters));
		
		// Authorization Handler
		$request->setAttribute("authorizationHandler", get_class($controller->getAuthorizationHandler()));
		
		// User
		$request->setAttribute("user", get_class($controller->getUser()));

		// User Container
		$request->setAttribute("userContainer", get_class($user->getContainer()));

		// Session Handler
		$sessionHandler = $controller->sessionHandler ? get_class($controller->sessionHandler) : "none";
		$request->setAttribute("sessionHandler", $sessionHandler);

		// Logger
		$logManager =& LogManager::getInstance();
		$lgs = $logManager->getLoggers();
		foreach ($lgs as $logger_name => $logger) {
			$lg = array(
				"name" =>  $logger_name . ' (' . get_class($logger) . ')',
				"priority" => $defines[$logger->getPriority()],
				"exit" =>  $defines[$logger->getExitPriority()],
			);
			foreach ($logger->appenders as $appender_name => $appender) {
                $lg["appenders"][] = $appender_name . ' (' . get_class($appender) . ')';
            }
			$loggers[] = $lg;
		}
		$request->setAttribute("loggers", $loggers);
		return VIEW_INDEX;
	}
	
	function getFilesByDir($directoryPath)
    {
		$files = array();
		$dh = opendir($directoryPath);
		if ($dh === false) {
			trigger_error("IndexAction::getFilesByDir(): failed to open directory \"$directoryPath\"", E_USER_WARNING);
			return false;
		}
		
		while ($file = readdir($dh)) {
			if ($file === false) {
				trigger_error("IndexAction::getFilesByDir(): failed to read directory \"$directoryPath\"", E_USER_WARNING);
				return false;
			}
			if (!preg_match("/^(\..*|svn$|CVS$)|~$/", $file)) $files[] = $file;
		}
		closedir($dh);
		return $files;
	}
}
