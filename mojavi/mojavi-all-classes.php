<?php
define('LEVEL_DEBUG', 1000);

define('LEVEL_INFO', 2000);

define('LEVEL_ERROR', 4000);

define('LEVEL_WARN', 3000);

define('LEVEL_FATAL', 5000);

define('RENDER_CLIENT', 1);

define('RENDER_VAR', 2);

define('REQ_NONE', 1);

define('REQ_GET',  2);

define('REQ_POST', 4);

define('VIEW_ALERT', 'alert');

define('VIEW_ERROR', 'error');

define('VIEW_INDEX', 'index');

define('VIEW_INPUT', 'input');

define('VIEW_NONE', NULL);

define('VIEW_SUCCESS', 'success');

define('AUTH_DIR', OPT_DIR . 'auth/');

define('FILTER_DIR', OPT_DIR . 'filters/');

define('LIB_DIR', BASE_DIR . 'lib/');

define('LOGGING_DIR', OPT_DIR . 'logging/');

define('MODULE_DIR', BASE_DIR . 'modules/');

define('RENDERER_DIR', OPT_DIR . 'renderers/');

define('SESSION_DIR', OPT_DIR . 'session/');

define('SQL_DIR', OPT_DIR . 'sql/');

define('TEMPLATE_DIR', BASE_DIR . 'templates/');

define('USER_DIR', OPT_DIR . 'user/');

define('UTIL_DIR', OPT_DIR . 'util/');

define('VALIDATOR_DIR', OPT_DIR . 'validators/');

define('GET_FORMAT', 1);

define('PATH_FORMAT', 2);

class FilterList
{
    var $filters;
    function FilterList ()
    {
        $this->filters = array();
    }
    function registerFilters (&$filterChain, &$controller, &$request, &$user)
    {
        $keys  = array_keys($this->filters);
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            $filterChain->register($this->filters[$keys[$i]]);
        }
    }
}
class Filter
{
    var $params;
    function Filter ()
    {
        $this->params = array();
    }
    function execute (&$filterChain, &$controller, &$request, &$user)
    {
        $error = 'Filter::execute(&$filterChain, &$controller, &$request, ' .
                 '&$user) must be overridden';
        trigger_error($error, E_USER_ERROR);
        exit;
    }
    function initialize ($params)
    {
        $this->params = array_merge($this->params, $params);
    }
}
class ExecutionFilter extends Filter
{
    function ExecutionFilter ()
    {
        parent::Filter();
    }
    function execute (&$filterChain, &$controller, &$request, &$user)
    {
        $execChain =& $controller->getExecutionChain();
        $action    =& $execChain->getAction($execChain->getSize() - 1);
        $actName   =  $controller->getCurrentAction();
        $modName   =  $controller->getCurrentModule();
        $method = $request->getMethod();
        if ($action->initialize($controller, $request, $user))
        {
            if ($action->isSecure())
            {
                $authHandler =& $controller->getAuthorizationHandler();
                if ($authHandler === NULL)
                {
                    trigger_error('Action requires security but no authorization ' .
                                  'handler has been registered', E_USER_NOTICE);
                } else if (!$authHandler->execute($controller, $request, $user, $action))
                {
                    return;
                }
            }
            if (($action->getRequestMethods() & $method) != $method)
            {
                $actView = $action->getDefaultView($controller, $request, $user);
            } else
            {
                $validManager =& new ValidatorManager;
                $action->registerValidators($validManager, $controller, $request,
                                            $user);
                if (!$validManager->execute($controller, $request, $user) ||
                    !$action->validate($controller, $request, $user))
                {
                    $actView = $action->handleError($controller, $request, $user);
                } else
                {
                    $actView = $action->execute($controller, $request, $user);
                }
            }
            if (is_string($actView) || $actView === NULL)
            {
                $viewMod  = $modName;
                $viewAct  = $actName;
                $viewName = $actView;
            } else if (is_array($actView))
            {
                $viewMod  = $actView[0];
                $viewAct  = $actView[1];
                $viewName = $actView[2];
            }
            if ($viewName != VIEW_NONE)
            {
/*
                if (!$controller->viewExists($viewMod, $viewAct, $viewName))
                {

                    $error = 'Module ' . $viewMod . ' does not contain view ' .
                             $viewAct . 'View_' . $viewName . ' or the file is ' .
                             'not readable';
                    trigger_error($error, E_USER_ERROR);
                    exit;
                }
*/
                if (!$controller->viewExists($viewMod, $viewAct, $viewName))
                {
                    $view = new DefaultView;
                } else {
                    $view =& $controller->getView($viewMod, $viewAct, $viewName);
                }

                $layout = $action->layout;
                if ($layout) {
                    $test = $view->initialize($controller,$request,$user);
                    $renderer =& $view->execute($controller, $request, $user);
                    $renderer->setMode(RENDER_VAR);
                    $partial = $renderer->fetchResult($controller, $request, $user);
                    $layout_renderer =& RendererUtils::getSmartyRenderer();
                    $layout_template = "{$layout}.html";
                    $layout_renderer->setTemplate($layout_template);
                    $layout_renderer->setAttribute('partial', $partial);
                    $layout_renderer->execute($controller, $request, $user);
                } else {
                    $test = $view->initialize($controller,$request,$user);
                    $renderer =& $view->execute($controller, $request, $user);

                    $renderer->execute($controller, $request, $user);
                }
                $view->cleanup($controller, $request, $user);
                $request->setAttributeByRef('org.mojavi.renderer', $renderer);
            }
        }
    }
}
class FilterChain
{
    var $index;
    var $filters;
    function FilterChain ()
    {
        $this->index = -1;
        $this->filters = array();
    }
    function execute (&$controller, &$request, &$user)
    {
        if (++$this->index < sizeof($this->filters))
        {
            $this->filters[$this->index]->execute($this, $controller,
                                                      $request, $user);
        }
    }
    function register (&$filter)
    {
        $this->filters[] =& $filter;
    }
}
require_once(LOGGING_DIR . 'ErrorLogger.class.php');
require_once(LOGGING_DIR . 'PatternLayout.class.php');
require_once(LOGGING_DIR . 'StdoutAppender.class.php');
class LogManager
{
    var $loggers;
    function LogManager ()
    {
         $this->loggers = array();
         $logger   =& new ErrorLogger();
         $layout   =& new PatternLayout('<b>%N</b> [%f:%l] %m%n');
         $appender =& new StdoutAppender($layout);
         $logger->addAppender('stdout', $appender);
         $this->loggers['default'] =& $logger;
    }
    function addLogger ($name, &$logger)
    {
        $instance =& LogManager::getInstance();
        $loggers  =& $instance->getLoggers();
        if (!isset($loggers[$name]))
        {
            $loggers[$name] =& $logger;
            return;
        }
        $error = 'LogManager already contains logger ' . $name;
        trigger_error($error, E_USER_ERROR);
        exit;
    }
    function cleanup ()
    {
        $keys  = array_keys($this->loggers);
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            $this->loggers[$keys[$i]]->cleanup();
        }
    }
    function & getDefaultLogger ()
    {
        $logManager =& LogManager::getInstance();
        return $logManager->getLogger();
    }
    function & getInstance ()
    {
        static $instance = NULL;
        if ($instance === NULL)
        {
            $instance = new LogManager;
        }
        return $instance;
    }
    function & getLogger ($name = 'default')
    {
        $instance =& LogManager::getInstance();
        $loggers  =& $instance->getLoggers();
        if (isset($loggers[$name]))
        {
            return $loggers[$name];
        }
	$null = NULL;
        return $null;
    }
    function & getLoggers ()
    {
        return $this->loggers;
    }
    function hasLogger ($name)
    {
        $instance =& LogManager::getInstance();
        $loggers  =& $instance->getLoggers();
        return isset($loggers[$name]);
    }
    function & removeLogger ($name)
    {
        $instance =& LogManager::getInstance();
        $loggers  =& $instance->getLoggers();
        if ($name != 'default' && isset($loggers[$name]))
        {
            $logger =& $loggers[$name];
            unset($loggers[$name]);
            return $logger;
        }
	$null = NULL;
        return $null;
    }
}
class Layout
{
    function Layout ()
    {
    }
    function & format (&$message)
    {
        $error = 'Layout::format(&$message) must be overridden';
        trigger_error($error, E_USER_ERROR);
        exit;
    }
}
class Appender
{
    var $layout;
    function Appender (&$layout)
    {
        $this->layout =& $layout;
    }
    function cleanup ()
    {
    }
    function & getLayout ()
    {
        return $this->layout;
    }
    function setLayout (&$layout)
    {
        $this->layout =& $layout;
    }
    function write ($message)
    {
        $error = 'Appender::write($message) must be overridden';
        trigger_error($error, E_USER_ERROR);
        exit;
    }
}
class Logger
{
    var $appenders;
    var $exitPriority;
    var $priority;
    function Logger ()
    {
        $this->exitPriority = LEVEL_ERROR;
        $this->priority     = LEVEL_ERROR;
    }
    function addAppender ($name, &$appender)
    {
        if (!isset($this->appenders[$name]))
        {
            $this->appenders[$name] =& $appender;
            return;
        }
        $error = 'Logger already has appender ' . $name;
        trigger_error($error, E_USER_ERROR);
        exit;
    }
    function cleanup ()
    {
        $keys  = array_keys($this->appenders);
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            $this->appenders[$keys[$i]]->cleanup();
        }
    }
    function & getAppender ($name)
    {
        if (isset($this->appenders[$name]))
        {
            return $this->appenders[$name];
        }
	$null = NULL;
        return $null;
    }
    function getExitPriority ()
    {
        return $this->exitPriority;
    }
    function getPriority ()
    {
        return $this->priority;
    }
    function log (&$message)
    {
        $msgPriority =& $message->getParameter('p');
        if ($this->priority == 0 || $msgPriority >= $this->priority)
        {
            $keys  = array_keys($this->appenders);
            $count = sizeof($keys);
            for ($i = 0; $i < $count; $i++)
            {
                $appender =& $this->appenders[$keys[$i]];
                $layout   =& $appender->getLayout();
                $result   =& $layout->format($message);
                $appender->write($result);
            }
        }
        if ($this->exitPriority > 0 && $msgPriority >= $this->exitPriority)
        {
            exit;
        }
    }
    function removeAppender ($name)
    {
        if (isset($this->appenders[$name]))
        {
            $appender =& $this->appenders[$name];
            $appender->cleanup();
            unset($this->appenders[$name]);
        }
    }
    function setExitPriority ($priority)
    {
        $this->exitPriority = $priority;
    }
    function setPriority ($priority)
    {
        $this->priority = $priority;
    }
}
class Message
{
    var $params;
    function Message ($params = NULL)
    {
        $this->params = ($params == NULL) ? array() : $params;
    }
    function & getParameter ($name)
    {
        if (isset($this->params[$name]))
        {
            return $this->params[$name];
        }
	$null = NULL;
        return $null;
    }
    function hasParameter ($name)
    {
        return isset($this->params[$name]);
    }
    function setParameter ($name, $value)
    {
        $this->params[$name] = $value;
    }
    function setParameterByRef ($name, &$value)
    {
        $this->params[$name] =& $value;
    }
}
class ExecutionChain
{
    var $chain;
    function ExecutionChain ()
    {
        $this->chain = array();
    }
    function addRequest ($modName, $actName, &$action)
    {
        $this->chain[] = array('module_name' => $modName,
                               'action_name' => $actName,
                               'action'      => &$action,
                               'microtime'   => microtime());
    }
    function & getAction ($index)
    {
        if (sizeof($this->chain) > $index && $index > -1)
        {
            return $this->chain[$index]['action'];
        }
	$null = NULL;
        return $null;
    }
    function getActionName ($index)
    {
        if (sizeof($this->chain) > $index && $index > -1)
        {
            return $this->chain[$index]['action_name'];
        }
        return NULL;
    }
    function getModuleName ($index)
    {
        if (sizeof($this->chain) > $index && $index > -1)
        {
            return $this->chain[$index]['module_name'];
        }
        return NULL;
    }
    function & getRequest ($index)
    {
        if (sizeof($this->chain) > $index && $index > -1)
        {
            return $this->chain[$index];
        }
	$null = NULL;
        return $null;
    }
    function & getRequests ()
    {
        return $this->chain;
    }
    function getSize ()
    {
        return sizeof($this->chain);
    }
}
class Validator
{
    var $message;
    var $params;
    function Validator ()
    {
        $this->message = NULL;
        $this->params  = array();
    }
    function execute (&$value, &$error, &$controller, &$request, &$user)
    {
        $error = 'Validator::execute(&$value, &$error, &$controller, ' .
                 '&$request, &$user) must be overridden';
        trigger_error($error, E_USER_ERROR);
        exit;
    }
    function getErrorMessage ()
    {
        return $this->message;
    }
    function & getParameter ($name)
    {
        if (isset($this->params[$name]))
        {
            return $this->params[$name];
        }
        return NULL;
    }
    function initialize ($params)
    {
        $this->params = array_merge($this->params, $params);
    }
    function setErrorMessage ($message)
    {
        $this->message = $message;
    }
    function setParameter ($name, $value)
    {
        $this->params[$name] = $value;
    }
    function setParameterByRef ($name, &$value)
    {
        $this->params[$name] =& $value;
    }
}
class ActionChain
{
    var $actions;
    var $preserve;
    function ActionChain ()
    {
        $this->actions  = array();
        $this->preserve = FALSE;
    }
    function execute (&$controller, &$request, &$user)
    {
        $keys  = array_keys($this->actions);
        $count = sizeof($keys);
        $renderMode = $controller->getRenderMode();
        $controller->setRenderMode(RENDER_VAR);
        for ($i = 0; $i < $count; $i++)
        {
            $action =& $this->actions[$keys[$i]];
            if ($this->preserve && $action['params'] != NULL)
            {
                $params   = array();
                $subKeys  = array_keys($action['params']);
                $subCount = sizeof($subKeys);
                for ($x = 0; $x < $subCount; $x++)
                {
                    if ($request->hasParameter($subKeys[$x]))
                    {
                        $params[$subKeys[$x]] = $request->getParameter($subKeys[$x]);
                    }
                }
            }
            if ($action['params'] != NULL)
            {
                $subKeys  = array_keys($action['params']);
                $subCount = sizeof($subKeys);
                for ($x = 0; $x < $subCount; $x++)
                {
                    $request->setParameterByRef($subKeys[$x],
                                                $action['params'][$subKeys[$x]]);
                }
            }
            $controller->forward($action['module'], $action['action']);
            $renderer =& $request->getAttribute('org.mojavi.renderer');
            if ($renderer !== NULL)
            {
                $action['result'] = $renderer->fetchResult($controller,
                                                           $request,
                                                           $user);
                $renderer->clearResult();
                $request->removeAttribute('org.mojavi.renderer');
            }
            if (isset($params))
            {
                $subKeys  = array_keys($params);
                $subCount = sizeof($subKeys);
                for ($x = 0; $x < $subCount; $x++)
                {
                    $request->setParameterByRef($subKeys[$x],
                                                $params[$subKeys[$x]]);
                }
                unset($params);
            }
        }
        $controller->setRenderMode($renderMode);
    }
    function & fetchResult ($regName)
    {
        if (isset($this->actions[$regName]['result']))
        {
            return $this->actions[$regName]['result'];
        }
	$null = NULL;
        return $null;
    }
    function register ($regName, $modName, $actName, $params = NULL)
    {
        $this->actions[$regName]['action'] = $actName;
        $this->actions[$regName]['module'] = $modName;
        $this->actions[$regName]['params'] = $params;
    }
    function setPreserve ($preserve)
    {
        $this->preserve = $preserve;
    }
}
class Renderer
{
    var $attributes;
    var $dir;
    var $engine;
    var $mode;
    var $result;
    var $template;
    function Renderer ()
    {
        $this->attributes = array();
        $this->dir        = NULL;
        $this->engine     = NULL;
        $this->mode       = RENDER_CLIENT;
        $this->result     = NULL;
        $this->template   = NULL;
    }
    function clearResult ()
    {
        if ($this->result != NULL)
        {
            unset($this->result);
        }
        $this->result = NULL;
    }
    function execute (&$controller, &$request, &$user)
    {
        $dir = NULL;
        if ($this->template == NULL)
        {
            $error = 'A template has not been specified';
            trigger_error($error, E_USER_ERROR);
            exit;
        }
        if ($this->isPathAbsolute($this->template))
        {
            $dir            = dirname($this->template) . '/';
            $this->template = basename($this->template);
        } else
        {
            $dir = ($this->dir == NULL)
                   ? $controller->getModuleDir() . 'templates/'
                   : $this->dir;
            if (!is_readable($dir . $this->template) &&
                 is_readable(TEMPLATE_DIR . $this->template))
            {
                $dir = TEMPLATE_DIR;
            }
        }
        if (is_readable($dir . $this->template))
        {
            $mojavi   =& $controller->getMojavi();
            $template =& $this->attributes;
            if ($this->mode == RENDER_VAR ||
                $controller->getRenderMode() == RENDER_VAR)
            {
                ob_start();
                require($dir . $this->template);
                $this->result = ob_get_contents();
                ob_end_clean();
            } else
            {
                require($dir . $this->template);
            }
        } else
        {
            $error = 'Template file ' . $dir . $this->template . ' does ' .
                     'not exist or is not readable';
            trigger_error($error, E_USER_ERROR);
            exit;
        }
    }
    function & fetchResult (&$controller, &$request, &$user)
    {
        if ($this->mode == RENDER_VAR ||
            $controller->getRenderMode() == RENDER_VAR)
        {
            if ($this->result == NULL)
            {
                $this->execute($controller, $request, $user);
            }
            return $this->result;
        }
	$null = NULL;
	return $null;
    }
    function & getAttribute ($name)
    {
        if (isset($this->attributes[$name]))
        {
            return $this->attributes[$name];
        }
	$null = NULL;
	return $null;
    }
    function & getEngine ()
    {
        return $this->engine;
    }
    function getMode ()
    {
        return $this->mode;
    }
    function getTemplateDir ()
    {
        return $this->dir;
    }
    function isPathAbsolute ($path)
    {
        if (strlen($path) >= 2)
        {
            if ($path{0} == '/' || $path{0} == "\\" || $path{1} == ':')
            {
                return TRUE;
            }
        }
        return FALSE;
    }
    function & removeAttribute ($name)
    {
        if (isset($this->attributes[$name]))
        {
            unset($this->attributes[$name]);
        }
    }
    function setArray ($array)
    {
        $this->attributes = array_merge($this->attributes, $array);
    }
    function setArrayByRef (&$array)
    {
        $keys  = array_keys($array);
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            $this->attributes[$keys[$i]] =& $array[$keys[$i]];
        }
    }
    function setAttribute ($name, $value)
    {
        $this->attributes[$name] = $value;
    }
    function setAttributeByRef ($name, &$value)
    {
        $this->attributes[$name] =& $value;
    }
    function setMode ($mode)
    {
        $this->mode = $mode;
    }
    function setTemplate ($template)
    {
        $this->template = $template;
    }
    function setTemplateDir ($dir)
    {
        $this->dir = $dir;
        if (substr($dir, -1) != '/')
        {
            $this->dir .= '/';
        }
    }
    function templateExists ($template, $dir = NULL)
    {
        if ($this->isPathAbsolute($template))
        {
            $dir      = dirname($template) . '/';
            $template = basename($template);
        } else if ($dir == NULL)
        {
            $dir = $this->dir;
            if (substr($dir, -1) != '/')
            {
                $dir .= '/';
            }
        }
        return (is_readable($dir . $template));
    }
}
class Request
{
    var $attributes;
    var $errors;
    var $method;
    var $params;
    function Request (&$params)
    {
        $this->attributes =  array();
        $this->errors     =  array();
        $this->method     = ($_SERVER['REQUEST_METHOD'] == 'POST')
                            ? REQ_POST : REQ_GET;
        $this->params     =& $params;
    }
    function & getAttribute ($name)
    {
        if (isset($this->attributes[$name]))
        {
            return $this->attributes[$name];
        }
	$null = NULL;
	return $null;
    }
    function getAttributeNames ()
    {
        return array_keys($this->attributes);
    }
    function & getAttributes ()
    {
        return $this->attributes;
    }
    function & getCookie ($name)
    {
        if (isset($_COOKIE[$name]))
        {
            return $_COOKIE[$name];
        }
	$null = NULL;
        return $null;
    }
    function getCookieNames ()
    {
        return array_keys($_COOKIE);
    }
    function & getCookies ()
    {
        return $_COOKIE;
    }
    function getError ($name)
    {
        return (isset($this->errors[$name])) ? $this->errors[$name] : NULL;
    }
    function & getErrors ()
    {
        return $this->errors;
    }
    function getMethod ()
    {
        return $this->method;
    }
    function & getParameter ($name, $value = 'NULL')
    {
        if (isset($this->params[$name]))
        {
            return $this->params[$name];
        } else if ($value != 'NULL')
        {
            return $value;
        }
	$null = NULL;
        return $null;
    }
    function getParameterNames ()
    {
        return array_keys($this->params);
    }
    function & getParameters ()
    {
        return $this->params;
    }
    function hasAttribute ($name)
    {
        return isset($this->attributes[$name]);
    }
    function hasCookie ($name)
    {
        return isset($_COOKIE[$name]);
    }
    function hasError ($name)
    {
        return isset($this->errors[$name]);
    }
    function hasErrors ()
    {
        return (sizeof($this->errors) > 0);
    }
    function hasParameter ($name)
    {
        return isset($this->params[$name]);
    }
    function & removeAttribute ($name)
    {
        if (isset($this->attributes[$name]))
        {
            $value =& $this->attributes[$name];
            unset($this->attributes[$name]);
            return $value;
        }
    }
    function & removeParameter ($name)
    {
        if (isset($this->params[$name]))
        {
            $value =& $this->params[$name];
            unset($this->params[$name]);
            return $value;
        }
    }
    function setAttribute ($name, $value)
    {
        $this->attributes[$name] =& $value;
    }
    function setAttributeByRef ($name, &$value)
    {
        $this->attributes[$name] =& $value;
    }
    function setError ($name, $message)
    {
        $this->errors[$name] =& $message;
    }
    function setErrors ($errors)
    {
        $keys  = array_keys($errors);
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            $this->errors[$keys[$i]] = $errors[$keys[$i]];
        }
    }
    function setMethod ($method)
    {
        $this->method = $method;
    }
    function setParameter ($name, $value)
    {
        $this->params[$name] = $value;
    }
    function setParameterByRef ($name, &$value)
    {
        $this->params[$name] =& $value;
    }
}
class View
{
    function View ()
    {
    }
    function cleanup (&$controller, &$request, &$user)
    {
    }
    function initialize (&$controller, &$request, &$user)
    {
	return true;
    }
    function & execute (&$controller, &$request, &$user)
    {
        $error = 'View::execute(&$controller, &$request, &$user) must be ' .
                 'overridden';
        trigger_error($error, E_USER_ERROR);
        exit;
    }
}
class Action
{
    //$params = array();
    function Action ()
    {
        $this->attrs =& $GLOBALS['controller']->request->attributes;
        $this->params =& $GLOBALS['controller']->request->params;
    }
    function execute (&$controller, &$request, &$user)
    {
        $error = 'Action::execute(&$controller, &$request, &$user) must be ' .
                 'overridden';
        trigger_error($error, E_USER_ERROR);
        exit;
    }
    function getDefaultView (&$controller, &$request, &$user)
    {
        return VIEW_INPUT;
    }
    function getPrivilege (&$controller, &$request, &$user)
    {
        return NULL;
    }
    function getRequestMethods ()
    {
        return REQ_GET | REQ_POST;
    }
    function handleError (&$controller, &$request, &$user)
    {
        return VIEW_ERROR;
    }
    function initialize (&$controller, &$request, &$user)
    {
        return TRUE;
    }
    function isSecure ()
    {
        return FALSE;
    }
    function registerValidators (&$validatorManager, &$controller, &$request,
                                 &$user)
    {
    }
    function validate (&$controller, &$request, &$user)
    {
        return TRUE;
    }
}
class User
{
    var $authenticated;
    var $attributes;
    var $container;
    var $secure;
    function User ()
    {
        $this->authenticated = NULL;
        $this->attributes    = NULL;
        $this->container     = NULL;
        $this->secure        = NULL;
    }
    function clearAll ()
    {
        $this->authenticated = FALSE;
        $this->attributes    = NULL;
        $this->attributes    = array();
        $this->secure        = NULL;
        $this->secure        = array();
    }
    function clearAttributes ()
    {
        $this->attributes = NULL;
        $this->attributes = array();
    }
    function & getAttribute ($name, $namespace = 'org.mojavi')
    {
        $namespace =& $this->getAttributes($namespace);
        if ($namespace != NULL && isset($namespace[$name]))
        {
            return $namespace[$name];
        }
	$null = NULL;
        return $null;
    }
    function getAttributeNames ($namespace = 'org.mojavi')
    {
        $namespace =& $this->getAttributes($namespace);
        return ($namespace != NULL) ? array_keys($namespace) : NULL;
    }
    function getAttributeNamespaces ()
    {
        return array_keys($this->attributes);
    }
    function & getAttributes ($namespace, $create = FALSE)
    {
        if (isset($this->attributes[$namespace]))
        {
            return $this->attributes[$namespace];
        } else if ($create)
        {
            $this->attributes[$namespace] = array();
            return $this->attributes[$namespace];
        }
	$null = NULL;
        return $null;
    }
    function & getContainer ()
    {
        return $this->container;
    }
    function hasAttribute ($name, $namespace = 'org.mojavi')
    {
        $namespace =& $this->getAttributes($namespace);
        return ($namespace != NULL && isset($namespace[$name])) ? TRUE : FALSE;
    }
    function isAuthenticated ()
    {
        return ($this->authenticated === TRUE) ? TRUE : FALSE;
    }
    function load ()
    {
        if ($this->container !== NULL)
        {
            $this->container->load($this->authenticated, $this->attributes,
                                   $this->secure);
        }
    }
    function mergeAttributes ($attributes)
    {
        $keys  = array_keys($attributes);
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            if (isset($this->attributes[$keys[$i]]))
            {
                $subKeys  = array_keys($attributes[$keys[$i]]);
                $subCount = sizeof($subKeys);
                for ($x = 0; $x < $subCount; $x++)
                {
                    $this->attributes[$keys[$i]][$subKeys[$x]] =& $attributes[$keys[$i]][$subKeys[$x]];
                }
            } else
            {
                $this->attributes[$keys[$i]] =& $attributes[$keys[$i]];
            }
        }
    }
    function & removeAttribute ($name, $namespace = 'org.mojavi')
    {
        $namespace =& $this->getAttributes($namespace);
        if ($namespace !== NULL && isset($namespace[$name]))
        {
            $value =& $namespace[$name];
            unset($namespace[$name]);
            return $value;
        }
	$null = NULL;
        return $null;
    }
    function removeAttributes ($namespace = 'org.mojavi')
    {
        $namespace =& $this->getAttributes($namespace);
        $namespace =  NULL;
    }
    function setAttribute ($name, $value, $namespace = 'org.mojavi')
    {
        $namespace        =& $this->getAttributes($namespace, TRUE);
        $namespace[$name] =  $value;
    }
    function setAttributeByRef ($name, &$value, $namespace = 'org.mojavi')
    {
        $namespace        =& $this->getAttributes($namespace, TRUE);
        $namespace[$name] =& $value;
    }
    function setAuthenticated ($status)
    {
        $this->authenticated = $status;
    }
    function setContainer (&$container)
    {
        $this->container =& $container;
    }
    function store ()
    {
        if ($this->container !== NULL)
        {
            $this->container->store($this->authenticated, $this->attributes,
                                    $this->secure);
        }
    }
}
class ValidatorManager
{
    var $validators;
    function ValidatorManager ()
    {
        $this->validators = array();
    }
    function execute (&$controller, &$request, &$user)
    {
        $keys    = array_keys($this->validators);
        $count   = sizeof($keys);
        $success = TRUE;
        for ($i = 0; $i < $count; $i++)
        {
            $param    =  $keys[$i];
            $value    =& $request->getParameter($param);
            $required =  $this->validators[$param]['required'];
            if (!$required && ($value == NULL || (is_string($value) && strlen($value) == 0)))
            {
            } else if ($value == NULL || (is_string($value) && strlen($value) == 0) || (is_array($value) && count($value)))
            {
                $message = $this->validators[$param]['message'];
                $request->setError($param, $message);
                $success = FALSE;
            } else if (isset($this->validators[$param]['validators']))
            {
                $error    = NULL;
                $subCount = sizeof($this->validators[$param]['validators']);
                for ($x = 0; $x < $subCount; $x++)
                {
                    $validator =& $this->validators[$param]['validators'][$x];
                    if (!$validator->execute($value, $error, $controller,
                                             $request, $user))
                    {
                        if ($validator->getErrorMessage() == NULL)
                        {
                            $request->setError($param, $error);
                        } else
                        {
                            $request->setError($param,
                                               $validator->getErrorMessage());
                        }
                        $success = FALSE;
                        break;
                    }
                }
            }
        }
        return $success;
    }
    function register ($param, &$validator)
    {
        if (!isset($this->validators[$param]))
        {
            $this->validators[$param] = array();
        }
        if (!isset($this->validators[$param]['validators']))
        {
            $this->validators[$param]['validators'] = array();
        }
        $this->validators[$param]['validators'][] =& $validator;
        if (!isset($this->validators[$param]['required']))
        {
            $this->setRequired($param, TRUE);
        }
    }
    function setRequired ($name, $required, $message = 'Required')
    {
        if (!isset($this->validators[$name]))
        {
            $this->validators[$name] = array();
        }
        $this->validators[$name]['required'] = $required;
        $this->validators[$name]['message']  = $message;
    }
}
require_once(USER_DIR . 'SessionContainer.class.php');
class Controller
{
    var $authorizationHandler;
    var $contentType;
    var $currentAction;
    var $currentModule;
    var $execChain;
    var $mojavi;
    var $renderMode;
    var $request;
    var $requestAction;
    var $requestModule;
    var $sessionHandler;
    var $user;
    function Controller ($contentType = 'html')
    {
        $this->contentType   =  $contentType;
        $this->currentAction =  NULL;
        $this->currentModule =  NULL;
        $this->execChain     =& new ExecutionChain;
        $this->renderMode    =  NULL;
        $this->requestAction =  NULL;
        $this->requestModule =  NULL;
        $this->authorizationHandler =  NULL;
        $this->request              =& new Request($this->parseParameters());
        $this->mojavi               =  array();
        $this->sessionHandler       =  NULL;
        $this->user                 =  NULL;
    }
    function actionExists ($modName, $actName)
    {
        $file = BASE_DIR . 'modules/' . $modName . '/actions/' . $actName .
                'Action.class.php';
        return (is_readable($file));
    }
    function dispatch ($modName = NULL, $actName = NULL)
    {
        $logger =& LogManager::getLogger();
        set_error_handler(array(&$logger, 'standard'));
        if ($this->user === NULL)
        {
            $this->user =& new User;
        }
        if (USE_SESSIONS)
        {
            if ($this->sessionHandler !== NULL)
            {
                session_set_save_handler(array(&$this->sessionHandler, 'open'),
                                         array(&$this->sessionHandler, 'close'),
                                         array(&$this->sessionHandler, 'read'),
                                         array(&$this->sessionHandler, 'write'),
                                         array(&$this->sessionHandler, 'destroy'),
                                         array(&$this->sessionHandler, 'gc'));
            }
            if($this->user->getContainer() == NULL)
	    {
	    	$this->user->setContainer(new SessionContainer);
	    }
        }
        $this->user->load();
        $mojavi  =& $this->mojavi;
        $request =& $this->request;
        if ($modName == NULL && !$request->hasParameter(MODULE_ACCESSOR) &&
            $actName == NULL && !$request->hasParameter(ACTION_ACCESSOR))
        {
            $actName = DEFAULT_ACTION;
            $modName = DEFAULT_MODULE;
        } else
        {
            if ($modName == NULL)
            {
                $modName = $request->getParameter(MODULE_ACCESSOR);
            }
            if ($actName == NULL)
            {
                $actName = $request->getParameter(ACTION_ACCESSOR);
                if ($actName == NULL)
                {
                    if ($this->actionExists($modName, 'Index'))
                    {
                        $actName = 'Index';
                    }
                }
            }
        }
        $actName = preg_replace('/[^a-z0-9\-_]+/i', '', $actName);
        $modName = preg_replace('/[^a-z0-9\-_]+/i', '', $modName);
        $this->requestAction      = $actName;
        $this->requestModule      = $modName;
        $mojavi['request_action'] = $actName;
        $mojavi['request_module'] = $modName;
        $mojavi['controller_path']     = $this->getControllerPath();
        $mojavi['current_action_path'] = $this->getControllerPath($modName,
                                                                  $actName);
        $mojavi['current_module_path'] = $this->getControllerPath($modName);
        $mojavi['request_action_path'] = $this->getControllerPath($modName,
                                                                  $actName);
        $mojavi['request_module_path'] = $this->getControllerPath($modName);
        $this->forward($modName, $actName);
        $this->user->store();
        if ($this->sessionHandler !== NULL)
        {
            $this->sessionHandler->cleanup();
        }
        $logManager =& LogManager::getInstance();
        $logManager->cleanup();
    }
    function forward ($modName, $actName)
    {
        if ($this->currentModule == $modName &&
            $this->currentAction == $actName)
        {
            $error = 'Recursive forward on module ' . $modName . ', action ' .
                     $actName;
            trigger_error($error, E_USER_ERROR);
            exit;
        }
        if (is_readable(MODULE_DIR . $modName . '/config.php'))
        {
            require_once(MODULE_DIR . $modName . '/config.php');
        }
        if ($this->actionExists($modName, $actName))
        {
            $action =& $this->getAction($modName, $actName);
        } else
        {
            $action = NULL;
        }
        $oldAction = $this->currentAction;
        $oldModule = $this->currentModule;
        $this->execChain->addRequest($modName, $actName, $action);
        $this->updateCurrentVars($modName, $actName);
        if (!AVAILABLE)
        {
            $actName = UNAVAILABLE_ACTION;
            $modName = UNAVAILABLE_MODULE;
            if (!$this->actionExists($modName, $actName))
            {
                $error = 'Invalid configuration setting(s): ' .
                         'UNAVAILABLE_MODULE (' . $modName . ') or ' .
                         'UNAVAILABLE_ACTION (' . $actName . ')';
                trigger_error($error, E_USER_ERROR);
                exit;
            }
            $action =& $this->getAction($modName, $actName);
            $this->execChain->addRequest($modName, $actName, $action);
            $this->updateCurrentVars($modName, $actName);
        } else if ($action === NULL)
        {
            $actName = ERROR_404_ACTION;
            $modName = ERROR_404_MODULE;
            if (!$this->actionExists($modName, $actName))
            {
                $error = 'Invalid configuration setting(s): ' .
                         'ERROR_404_MODULE (' . $modName . ') or ' .
                         'ERROR_404_ACTION (' . $actName . ')';
                trigger_error($error, E_USER_ERROR);
                exit;
            }
            $action =& $this->getAction($modName, $actName);
            $this->execChain->addRequest($modName, $actName, $action);
            $this->updateCurrentVars($modName, $actName);
        }
        $filterChain =& new FilterChain;
        $this->mapGlobalFilters($filterChain);
        $this->mapModuleFilters($filterChain, $modName);
        $filterChain->register(new ExecutionFilter);
        $filterChain->execute($this, $this->request, $this->user);
        $this->updateCurrentVars($oldModule, $oldAction);
    }
    function genURL ($params)
    {
        $url = SCRIPT_PATH;
        if (URL_FORMAT == PATH_FORMAT)
        {
            $divider  = '/';
            $equals   = '/';
            $url     .= '/';
        } else
        {
            $divider  = '&';
            $equals   = '=';
            $url     .= '?';
        }
        $keys  = array_keys($params);
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            if ($i > 0)
            {
                $url .= $divider;
            }
            $url .= urlencode($keys[$i]) . $equals .
                    urlencode($params[$keys[$i]]);
        }
        return $url;
    }
    function & getAction ($modName, $actName)
    {
        $file = BASE_DIR . 'modules/' . $modName . '/actions/' . $actName .
                'Action.class.php';
        require_once($file);
        $action = $actName . 'Action';
        $modAction = $modName . '_' . $action;
        if (class_exists($modAction))
        {
            $action =& $modAction;
        }
	$tmp_action = & new $action;
        return $tmp_action;
    }
    function & getAuthorizationHandler ()
    {
        return $this->authorizationHandler;
    }
    function getContentType ()
    {
        return $this->contentType;
    }
    function getControllerPath ($modName = NULL, $actName = NULL)
    {
        $path = SCRIPT_PATH;
        if ($modName != NULL)
        {
            $path .= (URL_FORMAT == GET_FORMAT)
                     ? '?' . MODULE_ACCESSOR . '=' . $modName
                     : '/' . MODULE_ACCESSOR . '/' . $modName;
        }
        if ($actName != NULL)
        {
            $sep = ($path == SCRIPT_PATH) ? '?' : '&';
            $path .= (URL_FORMAT == GET_FORMAT)
                     ? $sep . ACTION_ACCESSOR . '=' . $actName
                     : '/' . ACTION_ACCESSOR . '/' . $actName;
        }
        return $path;
    }
    function getCurrentAction ()
    {
        return $this->currentAction;
    }
    function getCurrentModule ()
    {
        return $this->currentModule;
    }
    function & getExecutionChain ()
    {
        return $this->execChain;
    }
    function & getInstance ($contentType = 'html')
    {
        static $instance;
        if ($instance === NULL)
        {
            $instance = new Controller($contentType);
        }
        return $instance;
    }
    function getModuleDir ()
    {
        return (BASE_DIR . 'modules/' . $this->currentModule . '/');
    }
    function & getMojavi ()
    {
        return $this->mojavi;
    }
    function getRenderMode ()
    {
        return $this->renderMode;
    }
    function & getRequest ()
    {
        return $this->request;
    }
    function getRequestAction ()
    {
        return $this->requestAction;
    }
    function getRequestModule ()
    {
        return $this->requestModule;
    }
    function & getSessionHandler ()
    {
        return $this->sessionHandler;
    }
    function & getUser ()
    {
        return $this->user;
    }
    function & getView ($modName, $actName, $viewName)
    {
        $file = BASE_DIR . 'modules/' . $modName . '/views/' . $actName .
                'View_' . $viewName . '.class.php';
        require_once($file);
        $view =  $actName . 'View';
        $modView = $modName . '_' . $view;
        if (class_exists($modView))
        {
            $view =& $modView;
        }
	$tmp_view = &new $view;
        return $tmp_view;
    }
    function mapGlobalFilters (&$filterChain)
    {
        static $list;
        if (!isset($list))
        {
            $file = BASE_DIR . 'GlobalFilterList.class.php';
            if (is_readable($file))
            {
                require_once($file);
                $list = new GlobalFilterList;
                $list->registerFilters($filterChain, $this, $this->request,
                                       $this->user);
            }
        } else
        {
            $list->registerFilters($filterChain, $this, $this->request,
                                   $this->user);
        }
    }
    function mapModuleFilters (&$filterChain, $modName)
    {
        static $cache;
        if (!isset($cache))
        {
            $cache = array();
        }
        $listName = $modName . 'FilterList';
        if (!isset($cache[$listName]))
        {
            $file = BASE_DIR . 'modules/' . $modName . '/' . $listName .
                    '.class.php';
            if (is_readable($file))
            {
                require_once($file);
                $list             =& new $listName;
                $cache[$listName] =& $list;
                $list->registerFilters($filterChain, $this, $this->request,
                                       $this->user);
            }
        } else
        {
            $cache[$listName]->registerFilters($filterChain, $this,
                                               $this->request, $this->user);
        }
    }
    function & parseParameters ()
    {
        $values = array();
        $values = array_merge($values, $_GET);
        if (URL_FORMAT == PATH_FORMAT                                 &&
            ((PATH_INFO_ARRAY == 1 && isset($_SERVER[PATH_INFO_KEY])) ||
             (PATH_INFO_ARRAY == 2 && isset($_ENV[PATH_INFO_KEY]))))
        {
            if (PATH_INFO_ARRAY == 1)
            {
                $array =& $_SERVER;
            } else
            {
                $array =& $_ENV;
            }
            $getArray = explode('/', trim($array[PATH_INFO_KEY], '/'));
            $count    = sizeof($getArray);
            for ($i = 0; $i < $count; $i++)
            {
                if ($count > ($i + 1))
                {
                    $values[$getArray[$i]] =& $getArray[++$i];
                }
            }
        }
        $values = array_merge($values, $_POST);
        if (ini_get('magic_quotes_gpc') == 1 && sizeof($values) > 0)
        {
            $this->stripSlashes($values);
        }
        return $values;
    }
    function redirect ($url)
    {
        header('Location: ' . $url);
    }
    function setAuthorizationHandler (&$handler)
    {
        $this->authorizationHandler =& $handler;
    }
    function setContentType ($contentType)
    {
        $this->contentType = $contentType;
    }
    function setRenderMode ($mode)
    {
        $this->renderMode = $mode;
    }
    function setSessionHandler (&$handler)
    {
        $this->sessionHandler =& $handler;
    }
    function setUser (&$user)
    {
        $this->user =& $user;
    }
    function stripSlashes (&$params)
    {
        $keys  = array_keys($params);
        $count = sizeof($keys);
        for ($i = 0; $i < $count; $i++)
        {
            $value =& $params[$keys[$i]];
            if (is_array($value))
            {
                $this->stripSlashes($value);
            } else
            {
                $value = stripslashes($value);
            }
        }
    }
    function updateCurrentVars ($modName, $actName)
    {
        $mojavi =& $this->mojavi;
        $this->currentModule = $modName;
        $this->currentAction = $actName;
        $mojavi['current_action'] = $actName;
        $mojavi['current_module'] = $modName;
        $mojavi['module_dir']   = BASE_DIR . 'modules/';
        $mojavi['template_dir'] = BASE_DIR . 'modules/' . $modName .
                                  '/templates/';
        $mojavi['current_action_path'] = $this->getControllerPath($modName,
                                                                  $actName);
        $mojavi['current_module_path'] = $this->getControllerPath($modName);
    }
    function viewExists ($modName, $actName, $viewName)
    {
        $file = BASE_DIR . 'modules/' . $modName . '/views/' . $actName .
                'View_' . $viewName . '.class.php';
        return (is_readable($file));
    }
}

class DefaultView extends View
{
    function &execute(&$controller, &$request, &$user)
    {
        $ext = $request->hasAttribute('ext') ? $request->getAttribute('ext') : 'html';
        switch ($ext) {
        case "xml":
        case "rdf":
        case "rss":
        case "opml":
            header('Content-Type: application/xml; charset=utf-8');
            break;
        case "txt":
            header('Content-Type: text/plain; charset=utf-8');
            break;
        case "js":
            header('Content-Type: text/javascript; charset=utf-8');
            break;
        default:
        case "html":
        }
        $template = $controller->mojavi['current_action'].".{$ext}";
        $renderer =& RendererUtils::getSmartyRenderer();
        $renderer->setTemplate($template);
        return $renderer;
    }
}

ini_set('display_errors', DISPLAY_ERRORS);
?>
