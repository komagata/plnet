<?php
class CSSAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member = DB_DataObject::factory('member');
        $member->get('account', $request->getParameter('account'));

        $ct = DB_DataObject::factory('custom_template');
        $ct->get('member_id', $member->id);
        header('Content-Type: text/css');
        echo $ct->template;
        return VIEW_NONE;
    }
}
?>
