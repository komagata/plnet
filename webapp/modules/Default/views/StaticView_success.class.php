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
 * This is the only view for StaticAction. If this gets called, a template will
 * be rendered.
 */
class StaticView extends View
{

    function & execute (&$controller, &$request, &$user)
    {

        // create a new Renderer instance
        $renderer =& new Renderer($controller, $request, $user);

        // retrieve the template
        $template = $request->getAttribute('StaticTemplate');

        // set the template
        $renderer->setTemplate($template);

        return $renderer;

    }

}

?>