<?php
require_once 'MemberUtils.php';
require_once 'FeedUtils.php';

class ListAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        switch (true) {
        case ($request->hasParameter('cc_name')) :
            $display = 'content_category';
            $request->setAttribute('cc_name', $request->getParameter('cc_name'));
            break;
        default:
        }

        $request->setAttribute('display', $display);
        return VIEW_SUCCESS;
    }
}
?>

