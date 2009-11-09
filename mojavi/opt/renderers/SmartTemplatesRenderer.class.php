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
 * SmartTemplatesRenderer renders a template using the SmartTemplates template
 * engine.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package renderers
 * @since   1.0
 */
class SmartTemplatesRenderer extends Renderer
{

    /**
     * Create a new SmartTemplatesRenderer instance.
     *
     * @access public
     * @since  1.0
     */
    function SmartTemplatesRenderer ()
    {

        parent::Renderer();

        $this->engine =& new SmartTemplate;

    }

    /**
     * Render the view.
     *
     * <br/><br/>
     *
     * <note>
     *     This should never be called manually.
     * </note>
     *
     * @param Controller A Controller instance.
     * @param Request    A Request instance.
     * @param User       A User instance.
     *
     * @access public
     * @since  1.0
     */
    function execute (&$controller, &$request, &$user)
    {

        if ($this->template == NULL)
        {

            $error = 'A SmartTemplates template has not been specified';

            trigger_error($error, E_USER_ERROR);

            return;

        }

        // assign smart templates variables
        $this->setAttributeByRef('controller', $controller);
        $this->setAttributeByRef('mojavi',     $controller->getMojavi());
        $this->setAttributeByRef('request',    $request);
        $this->setAttributeByRef('user',       $user);

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

            $this->engine->template_dir = $dir;
            $this->engine->set_templatefile($this->template);

            if ($this->mode == RENDER_VAR ||
                $controller->getRenderMode() == RENDER_VAR)
            {

                $this->result = $this->engine->result();

            } else
            {

                $this->engine->output();

            }

        } else
        {

            $error = 'Template file ' . $dir . $this->template . ' does ' .
                     'not exist or is not readable';

            trigger_error($error, E_USER_ERROR);

        }

    }

    /**
     * Retrieve an attribute.
     *
     * @param string An attribute name.
     *
     * @return mixed An attribute value, if the given attribute exists,
     *               otherwise <b>NULL</b>.
     *
     * @access public
     * @since  1.0
     */
    function & getAttribute ($name)
    {

        if (isset($this->engine->data[$name]))
        {

            $attribute =& $this->engine->data[$name];

            return $attribute;

        }

	$null = NULL;
        return $null;
    }

    /**
     * Remove an attribute.
     *
     * @param string An attribute name.
     *
     * @access public
     * @since  1.0
     */
    function removeAttribute ($name)
    {

        if (isset($this->engine->data[$name]))
        {

            unset($this->engine->data[$name]);

        }

    }

    /**
     * Set multiple attributes by using an associative array.
     *
     * @param array An associative array of attributes.
     *
     * @access public
     * @since  2.0
     */
    function setArray ($array)
    {

        $keys  = array_keys($array);
        $count = sizeof($keys);

        for ($i = 0; $i < $count; $i++)
        {

            $this->engine->assign($keys[$i], $array[$keys[$i]]);

        }

    }

    /**
     * Set multiple attributes by using a reference to an associative array.
     *
     * @param array An associative array of attributes.
     *
     * @access public
     * @since  2.0
     */
    function setArrayByRef (&$array)
    {

        $keys  = array_keys($array);
        $count = sizeof($keys);

        for ($i = 0; $i < $count; $i++)
        {

            $this->engine->data[$keys[$i]] =& $array[$keys[$i]];

        }

    }

    /**
     * Set an attribute.
     *
     * @param string An attribute name.
     * @param mixed  An attribute value.
     *
     * @access public
     * @since  1.0
     */
    function setAttribute ($name, $value)
    {

        $this->engine->assign($name, $value);

    }

    /**
     * Set an attribute by reference.
     *
     * @param string An attribute name.
     * @param mixed  An attribute value.
     *
     * @access public
     * @since  1.0
     */
    function setAttributeByRef ($name, &$value)
    {

        $this->engine->data[$name] =& $value;

    }

}

?>
