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
 * PrivilegeUser allows privileges to be assigned to a user.
 *
 * @author  Sean Kerr
 * @package mojavi
 * @package user
 * @since   2.0
 */
class PrivilegeUser extends User
{

    /**
     * Create a new PrivilegeUser instance.
     *
     * @access public
     * @since  2.0
     */
    function PrivilegeUser ()
    {

        parent::User();

        $this->secure = array();

    }

    /**
     * Add a privilege.
     *
     * @param string A privilege name.
     * @param string A privilege namespace.
     *
     * @access public
     * @since  2.0
     */
    function addPrivilege ($name, $namespace = 'org.mojavi')
    {

        $namespace        =& $this->getPrivilegeNamespace($namespace, TRUE);
        $namespace[$name] =  TRUE;

    }

    /**
     * Clear all privilege namespaces and their associated privileges.
     *
     * @access public
     * @since  2.0
     */
    function clearPrivileges ()
    {

        $this->secure = NULL;
        $this->secure = array();

    }

    /**
     * Retrieve a privilege namespace.
     *
     * @param string A privilege namespace.
     * @param bool   Whether or not to auto-create the privilege namespace if it
     *               doesn't already exist.
     *
     * @return array A privilege namespace, if the given namespace exists,
     *               otherwise <b>NULL</b>.
     *
     * @access public
     * @since  2.0
     */
    function & getPrivilegeNamespace ($namespace, $create = FALSE)
    {

        if (isset($this->secure[$namespace]))
        {

            return $this->secure[$namespace];

        } else if ($create)
        {

            $this->secure[$namespace] = array();

            return $this->secure[$namespace];

        }

	$null = NULL;
        return $null;
    }

    /**
     * Retrieve an indexed array of privilege namespaces.
     *
     * @return array An array of privileges.
     *
     * @access public
     * @since  2.0
     */
    function getPrivilegeNamespaces ()
    {

        return array_keys($this->secure);

    }

    /**
     * Retrieve an indexed array of namespace privileges.
     *
     * @param string A privilege namespace.
     *
     * @return array An array of privilege names, if the given namespace exists,
     *               otherwise <b>NULL</b>.
     *
     * @access public
     * @since  2.0
     */
    function & getPrivileges ($namespace = 'org.mojavi')
    {

        $namespace =& $this->getPrivilegeNamespace($namespace);

        if ($namespace !== NULL)
        {

            return array_keys($namespace);

        }

	$null = NULL;
        return $null;
    }

    /**
     * Determine if the user has a privilege.
     *
     * @param string A privilege name.
     * @param string A privilege namespace.
     *
     * @return bool <b>TRUE</b>, if the user has the given privilege, otherwise
     *              <b>FALSE</b>.
     *
     * @access public
     * @since  2.0
     */
    function hasPrivilege ($name, $namespace = 'org.mojavi')
    {

        $namespace =& $this->getPrivilegeNamespace($namespace);

        return ($namespace !== NULL && isset($namespace[$name])) ? TRUE : FALSE;

    }

    /**
     * Load user data from the container.
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
    function load ()
    {

        if ($this->container !== NULL)
        {

            parent::load();

        }

    }

    /**
     * Merge a new indexed array of privileges with the existing array.
     *
     * @param array An indexed array of privileges.
     *
     * @access public
     * @since  2.0
     */
    function mergePrivileges ($privileges)
    {

        $keys  = array_keys($privileges);
        $count = sizeof($keys);

        for ($i = 0; $i < $count; $i++)
        {

            if (isset($this->secure[$keys[$i]]))
            {

                // namespace already exists, merge values only
                $subKeys  = array_keys($privileges[$keys[$i]]);
                $subCount = sizeof($subKeys);

                for ($x = 0; $x < $subCount; $x++)
                {

                    $this->secure[$keys[$i]][$subKeys[$x]] = TRUE;

                }

            } else
            {

                // add entire namespace and related privileges
                $this->secure[$keys[$i]] =& $privileges[$keys[$i]];

            }

        }

    }

    /**
     * Remove a privilege.
     *
     * @param string A privilege name.
     * @param string A privilege namespace.
     *
     * @access public
     * @since  2.0
     */
    function & removePrivilege ($name, $namespace = 'org.mojavi')
    {

        $namespace =& $this->getPrivilegeNamespace($namespace);

        if ($namespace !== NULL && isset($namespace[$name]))
        {

            unset($namespace[$name]);

        }

    }

    /**
     * Remove a privilege namespace and all associated privileges.
     *
     * @param string A privilege namespace.
     *
     * @access public
     * @since  2.0
     */
    function removePrivileges ($namespace = 'org.mojavi')
    {

        $namespace =& $this->getPrivilegeNamespace($namespace);
        $namespace =  NULL;

    }

}

?>
