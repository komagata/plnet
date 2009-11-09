<?php
class IndexAction extends Action
{
    function isSecure()
    {
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $request->setAttribute(
            'member',
            $user->getAttribute('member', GLU_NS)
        );
        return VIEW_SUCCESS;
    }
}
?>
