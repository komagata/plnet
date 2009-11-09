<?php
class HeaderAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member = $user->getAttribute('member', GLU_NS);
        $request->setAttribute('member', $member);

        $request->setAttribute('selected', '');

        return VIEW_SUCCESS;
    }
}
?>
