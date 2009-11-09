<?php
class MenuAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member = $user->getAttribute('member', GLU_NS);
        $request->setAttribute('member', $member);
        $request->setAttribute('request_sig', $controller->requestModule.
        '_' . $controller->requestAction);
        return VIEW_SUCCESS;
    }
}
?>
