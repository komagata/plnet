<?php
require_once LIB_DIR . 'MailUtils.php';

class ConfirmMailAction extends Action
{
    function getRequestMethods()
    {
        return REQ_POST;
    }

    function validate (&$controller, &$request, &$user)
    {
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        //DB_DataObject::debuglevel(3);
        $tmp_account = $request->getParameter('account');
        $tmp_member  = DB_DataObject::factory('member_temporary');
        $tmp_member->get('account', $tmp_account);
        $key     = $tmp_member->activate_key;
        $to      = $tmp_member->email;
        $account = $tmp_member->account;

        $activate_url = SCRIPT_PATH.'activate/'.rawurlencode($key);
        $request->setAttribute('activate_url', $activate_url);

        // create mail.
        $subject  = msg('confirm mail subject');
        $message  = sprintf(msg('confirm mail body'), $activate_url);

        $from     = array('From' => EMAIL_FROM);
        MailUtils::send($to, $subject, $message, $from, array(), 'mail');
        return VIEW_NONE;
    }
}
?>
