<?php
require_once 'String/Random.php';
require_once VALIDATOR_DIR . 'EmailValidator.class.php';
require_once VALIDATOR_DIR . 'RegexValidator.class.php';

class RegisterAction extends Action
{
    function getRequestMethods()
    {
        return REQ_POST;
    }

    function validate (&$controller, &$request, &$user)
    {
        // verify account
        global $account_deny;
        $account         = $request->getParameter('account');
        $member          = DB_DataObject::factory('member');
        $tmp_member      = DB_DataObject::factory('member_temporary');
        $rows_member     = $member->get('account', $account);
        $rows_tmp_member = $tmp_member->get('account', $account);

        if ($rows_member > 0 || $rows_tmp_member > 0) {
            $request->setError('account', msg('account is required'));
            return false;
        }
        if (in_array($account, $account_deny)) {
            $request->setError('account', msg('account is banned'));
            return false;
        }

        // verify password
        if ($request->getParameter('password') != $request->getParameter('password_verify')) {
            $request->setError('password', msg('password dose not match'));
            return false;
        }
        return true;
    }


    function registerValidators(&$validatorManager, &$controller, &$request, &$user)
    {
        $validatorManager->setRequired('account', true, msg('account is required'));
        $regexValidator = new RegexValidator();
        $regexValidator->setParameter('pattern_error', msg('account have invalid character'));
        $regexValidator->setParameter('pattern', PATTERN_ACCOUNT);
        $validatorManager->register('account', $regexValidator);

        $validatorManager->setRequired('password', true, msg('password is required'));
        $validatorManager->setRequired('email', true, msg('email is required'));
        $emailValidator = new EmailValidator();
        $emailValidator->setParameter('email_error', msg('email is wrong'));
        $validatorManager->register('email', $emailValidator);
    }

    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $member = DB_DataObject::factory('member_temporary');
        $member->account      = $account;
        $member->password     = sha1($request->getParameter('password'));
        $member->email        = $request->getParameter('email');
        $member->createdtime  = date("Y-m-d H:i:s");
        $member->activate_key = sha1($account.String_Random::getRandRegex('\w{10}'));
        $member_id = $member->insert();
        if ($member_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if(PEAR::isError($error)){
                trigger_error($error->toString(), E_USER_ERROR);
                exit;
            }
            return VIEW_NONE;
        }

        $controller->forward($controller->currentModule, 'ConfirmMail');
        return VIEW_SUCCESS;
    }
}
?>
