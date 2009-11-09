<?php
class ActivateAction extends Action
{
    function isSecure()
    {
        return false;
    }

    function getRequestMethods()
    {
        return REQ_GET;
    }

    function validate (&$controller, &$request, &$user)
    {
        $key = urldecode($request->getParameter('key'));
        $tmp_member = DB_DataObject::factory('member_temporary');
        $row = $tmp_member->get('activate_key', $key);

        if ($row > 0) {
            return true;
        }
        return false;
    }

    function execute(&$controller, &$request, &$user)
    {
        $key = urldecode($request->getParameter('key'));

        $tmp_member = DB_DataObject::factory('member_temporary');
        $member     = DB_DataObject::factory('member');

        // move temporary member as real member.
        $tmp_member->get('activate_key', $key);
        $member->account     = $tmp_member->account;
        $member->password    = $tmp_member->password;
        $member->email       = $tmp_member->email;
        $member->design_id   = PLNET_DEFAULT_DESIGN_ID;
        $member->createdtime = date("Y-m-d H:i:s");
        $member_id = $member->insert();
        if ($member_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if (PEAR::isError($error)) {
                trigger_error($error->toString(), E_USER_ERROR);
                exit;
            }
            return VIEW_NONE;
        }
        // site
        $site = DB_DataObject::factory('site');
        $site->member_id = $member_id;
        $site->title = sprintf(msg('default site title'), $member->account);
        $site->description = msg('default site description');
        $site->createdtime = date("Y-m-d H:i:s");
        $site_id = $site->insert();
        if ($site_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if(PEAR::isError($error)){
                trigger_error($error->toString(), E_USER_ERROR);
                exit;
            }
            return VIEW_NONE;
        }

        // set authentication.
        $user->setAuthenticated(true);
        $user->addPrivilege('member', GLU_NS);
        $user->setAttribute('member', $member, GLU_NS);

        // delete temporary member. //TODO: FIXME
        $tmp_member_id = $tmp_member->delete();
        if ($tmp_member_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if (PEAR::isError($error)) {
                trigger_error($error->toString(), E_USER_ERROR);
                exit;
            }
            return VIEW_NONE;
        }
        Controller::redirect(SCRIPT_PATH . 'setting/feed');
        return VIEW_NONE;
    }
}
?>
