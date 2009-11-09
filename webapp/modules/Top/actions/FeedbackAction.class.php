<?php
class FeedbackAction extends Action
{
    var $layout = 'Public';

    function execute(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = 'Plnet &gt; '.msg('feedback');
        return VIEW_SUCCESS;
    }
}
?>
