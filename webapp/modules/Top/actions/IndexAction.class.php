<?php
class IndexAction extends Action
{
    var $layout = 'Public';

    function execute(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = 'Plnet';
        $this->attrs['DEV_FEED_URI'] = DEV_FEED_URI;
        return VIEW_SUCCESS;
    }
}
?>
