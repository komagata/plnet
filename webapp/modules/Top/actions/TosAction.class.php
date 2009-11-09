<?php
class TosAction extends Action
{
    var $layout = 'Public';

    function execute(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = 'Plnet &gt; '.msg('tos');
        return VIEW_SUCCESS;
    }
}
?>
