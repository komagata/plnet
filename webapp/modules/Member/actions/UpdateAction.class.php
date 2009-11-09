<?php
require_once VALIDATOR_DIR.'EmailValidator.class.php';

class UpdateAction extends Action
{
    var $layout = 'Admin';

    function initialize(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = "Plnet &gt; ".msg('setting')." &gt; ".msg('profile');

        // session member
        $ses_member = $user->getAttribute('member', GLU_NS);

        $member = DB_DataObject::factory('member');
        $member->get('id', $ses_member->id);
        if ($member->photo) {
            $member->photo_url = SCRIPT_PATH.'photo.php?member_id='.$member->id;
        }
        $request->setAttribute('member', $member);

        $request->setAttribute('languages', array(
            '' => msg('choose a language'),
            'en' => msg('english'),
            'ja' => msg('japanese')
        ));

        $request->setAttribute('genders', array(
            '' => msg('choose a gender'),
            1 => msg('male'),
            2 => msg('female')
        ));

        $request->setAttribute('key', session_id());
        return true;
    }

    function isSecure()
    {
        return true;
    }

    function getRequestMethods()
    {
        return REQ_POST;
    }

    function validate (&$controller, &$request, &$user)
    {
        if ($request->getParameter('password') != $request->getParameter('password-verify')) {
            $request->setError('password-verify', msg('password dosent match'));
            return false;
        }
        return true;
    }

    function registerValidators(&$validatorManager, &$controller, &$request, &$user)
    {
        $validatorManager->setRequired('email', true, msg('email is required'));
        $emailValidator = new EmailValidator();
        $emailValidator->params['email_error'] = msg('email format is wrong');
        $validatorManager->register('email', $emailValidator);
    }

    function execute(&$controller, &$request, &$user)
    {
        $member = DB_DataObject::factory('member');
        $member->get('id', $request->getParameter('id'));

        $member->firstname = $request->getParameter('firstname');
        $member->familyname = $request->getParameter('familyname');
        $member->nickname = $request->getParameter('nickname');

        // photo
        if (is_uploaded_file($_FILES['photo']['tmp_name'])) {
            $member->photo = file_get_contents($_FILES['photo']['tmp_name']);
        }

        $remove_photo = $request->hasParameter('remove_photo')
            ? $request->getParameter('remove_photo') : null;
        if ($remove_photo) {
            $member->photo = '';
        }

        $member->gender = $request->getParameter('gender');
        $member->homepage = $request->getParameter('homepage');

        // birth_date
        $year = $request->getParameter('birthdate_year')
            ? $request->getParameter('birthdate_year') : 0;
        $month = $request->getParameter('birthdate_month')
            ? $request->getParameter('birthdate_month') : 0;
        $day = $request->getParameter('birthdate_day')
            ? $request->getParameter('birthdate_day') : 0;
        if (checkdate($month, $day, $year)) {
            $birthdate = "{$year}-{$month}-{$day}";
        } else {
            $birthdate = 0;
        }
        $member->birthdate = $birthdate;

        // password
        if (strlen($request->getParameter('password')) > 0) {
            $member->password = sha1($request->getParameter('password'));
        }

        $member->email = $request->getParameter('email');
        $member->aim = $request->getParameter('aim');
        $member->yahoo = $request->getParameter('yahoo');
        $member->skype = $request->getParameter('skype');
        $member->msn = $request->getParameter('msn');
        $member->googletalk = $request->getParameter('googletalk');
        $member->language = $request->getParameter('language');
        $member->self_introduction = $request->getParameter('self_introduction');
        $member_id = $member->update();
        if ($member_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if(PEAR::isError($error)){
                trigger_error($error->toString(), E_USER_ERROR);
                exit;
            }
            return VIEW_NONE;
        }

        // send ping
        $site = DB_DataObject::factory('site');
        $site->get('member_id', $member->id);
        if ($site->show_profile) {
            $pingurl = 'http://pingthesemanticweb.com/rest/?url='.
                urlencode(SCRIPT_PATH.$member->account.'/foaf.rdf');

            $res = @file($pingurl);
            LogUtils::debug("Send ping to $pingurl. ".var_export($res, true));
            if (!$res) {
                trigger_error(
                    "Failed to send ping. url: $pingurl",
                    E_USER_NOTICE
                );
            }
        }

        $user->setAttribute('member', $member, GLU_NS);
        Controller::redirect(SCRIPT_PATH.'setting/user/changed');
        return VIEW_NONE;
    }
}
?>
