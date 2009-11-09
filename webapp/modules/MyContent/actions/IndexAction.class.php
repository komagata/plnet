<?php
class IndexAction extends Action
{
    var $layout = 'Admin';

    function isSecure() { return true; }

    function execute(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = "Plnet &gt; ".msg('setting')." &gt; ".msg('feed');
        return VIEW_SUCCESS;
    }
}
?>
