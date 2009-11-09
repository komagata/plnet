<?php
class AboutAction extends Action
{
    var $layout = 'Public';

    function execute(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = 'Plnet &gt; '.msg('about');
        return VIEW_SUCCESS;
    }
}
?>
