<?php
class DeveloperAction extends Action
{
    var $layout = 'Public';

    function execute(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = 'Plnet &gt; '.msg('developer');
        return VIEW_SUCCESS;
    }
}
?>
