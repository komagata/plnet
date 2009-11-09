<?php
class RemoveAction extends Action
{
    function isSecure()
    {
        return true;
    }

    function validate(&$controller, &$request, &$user)
    {
        // for CSRF
        if ($request->getParameter('key') != session_id())
            return false;

        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $s_member = $user->getAttribute('member', GLU_NS);
        $member = DB_DataObject::factory('member');
        $member->get('id', $s_member->id);
        $member_id = $member->delete();
        if ($member_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if(PEAR::isError($error)){
                trigger_error(__CLASS__.'::'.__FUNCTION__.'():'.
                $error->toString(), E_USER_ERROR);
                exit;
            }
            return VIEW_NONE;
        }

        $user->clearAll();
        Controller::redirect(SCRIPT_PATH.'setting/user/resigned');
        return VIEW_NONE;
    }
}
?>
