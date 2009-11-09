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
 * SmartyRenderer renders a template using the Smarty template engine.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package renderers
 * @since   1.0
 */
class SmartyRenderer extends Renderer
{

    /**
     * Create a new SmartyRenderer instance.
     *
     * @access public
     * @since  1.0
     */
    function SmartyRenderer ()
    {

        parent::Renderer();

        $this->engine =& new Smarty;

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

            $error = 'A Smarty template has not been specified';

            trigger_error($error, E_USER_ERROR);

            return;

        }

        // assign smarty variables
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

            if ($this->mode == RENDER_VAR ||
                $controller->getRenderMode() == RENDER_VAR)
            {

                $this->result = $this->engine->fetch($this->template);

            } else
            {

                $this->engine->display($this->template);

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

        $attribute =& $this->engine->get_template_vars($name);

        if ($attribute != NULL)
        {

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

        $this->engine->clear_assign($name);

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

        $this->engine->assign($array);

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

        $this->engine->assign_by_ref($array);

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

        $this->engine->assign_by_ref($name, $value);

    }

}

?>
