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
 * Static action allows you to use any specified file as a template.
 *
 * Please view the <YourModule>/config/StaticAction.config.php configuration
 * file.
 */
class StaticAction extends Action
{

    function execute (&$controller, &$request, &$user)
    {

        // retrieve the template
        $template = $request->getParameter(DEF_STATIC_VAR);

        if ($template == NULL)
        {

            $template = DEF_STATIC_TEM;

        } else if (strpos($template, DEF_STATIC_SEP) !== FALSE)
        {

            // replace all file separators
            $template = str_replace(DEF_STATIC_SEP, '/', $template);

        }

        $template = DEF_STATIC_DIR . $template;

        if (is_readable($template))
        {

            // template exists
            $request->setAttribute('StaticTemplate', $template);

            return VIEW_SUCCESS;

        }

        // template does not exist or is not readable
        // let's forward to the error 404 action
        $controller->forward(ERROR_404_MODULE, ERROR_404_ACTION);

        return VIEW_NONE;

    }

    function getDefaultView (&$controller, &$request, &$user)
    {

        return VIEW_SUCCESS;

    }

    function handleError (&$controller, &$request, &$user)
    {

        // don't handle errors, just redirect to error 404 action
        $controller->forward(ERROR_404_MODULE, ERROR_404_ACTION);

        return VIEW_NONE;

    }

    function registerValidators (&$validatorManager, &$controller, &$request,
                                 &$user)
    {

        // include configuration file
        require_once($controller->getModuleDir() .
                     'config/StaticAction.config.php');

        // include required validators
        require_once(VALIDATOR_DIR . 'RegexValidator.class.php');

        // execute patterns
        $patterns = explode("#", DEF_STATIC_PATS);
        $count    = sizeof($patterns);

        for ($i = 0; $i < $count; $i++)
        {

            $pattern = trim($patterns[$i]);

            if (strlen($pattern) > 0)
            {

                $match  = TRUE;
                $params = array('pattern' => $pattern);
                $pos    = strpos($pattern, '::');

                if ($pos !== FALSE)
                {

                    if (substr($pattern, $pos + 2) === '0')
                    {

                        $match = FALSE;

                    }

                    $pattern = substr($pattern, 0, $pos);
                    $params  = array('match'   => $match,
                                     'pattern' => $pattern);

                }

                $validator =& new RegexValidator;
                $params    =  $params;

                $validator->initialize($params, $controller, $request, $user);

                // register the validator so it's not required
                // if the parameter is set, validators will execute
                $validatorManager->register(DEF_STATIC_VAR, $validator);
                $validatorManager->setRequired(DEF_STATIC_VAR, FALSE);

                unset($validator);

            }

        }

    }

}

?>