<?php
require_once 'FOAFWriter.php';
require_once 'FriendUtils.php';

class ProfileDescriptionAction extends Action
{
    function initialize(&$controller, &$request, &$user) {
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $format = $request->hasParameter('format')
            ? $request->getParameter('format') : 'html';

        $member = DB_DataObject::factory('member');
        $member->get('account', $account);
        $member->photo_url = $member->photo
            ? SCRIPT_PATH.'photo.php?member_id='.$member->id 
            : PLNET_DEFAULT_PHOTO;

        $languages = array(
            'en' => msg('english'),
            'ja' => msg('japanese')
        );
        $genders = array(
            1 => msg('male'),
            2 => msg('female')
        );
        $genders4foaf = array(
            1 => 'male',
            2 => 'female'
        );

        $member->language_text = $languages[$member->language];
        $member->gender_text = $genders[$member->gender];

        if ($format === 'foaf') {

            // site
            $site = DB_DataObject::factory('site');
            $site->get('member_id', $member->id);
            if (!$site->show_profile) {
                $controller->redirect('/404.html');
                return VIEW_NONE;
            }

            $locale = $request->getAttribute('locale');
            $name = $locale === 'ja' 
                ? $member->familyname.' '.$member->firstname
                : $member->firstname.' '.$member->familyname;
            if ($name === ' ') $name = $member->nickname;

            $fw = new FOAFWriter;

            $profile = array(
                'nick' => $member->nickname, 
                'mbox_sha1sum' => $member->email, 
                'img' => $member->photo_url, 
            );

            if (!empty($member->firstname))
                $profile['firstName'] = $member->firstname;

            if (!empty($member->familyname))
                $profile['surname'] = $member->familyname;

            if (!empty($name))
                $profile['name'] = $name;

            if (!empty($member->gender))
                $profile['gender'] = $genders4foaf[$member->gender];

            if (!empty($member->self_introduction))
                $profile['bio'] = $member->self_introduction;

            if (!empty($member->aim))
                $profile['aimChatID'] = $member->aim;

            if (!empty($member->msn))
                $profile['msnChatID'] = $member->msn;

            if ($member->yahoo)
                $profile['yahooChatID'] = $member->yahoo;

            if ($member->googletalk)
                $profile['jabberID'] = $member->googletalk;

            $fw->setProfile($profile);

            $friends = FriendUtils::find_by_account($account);
            if (count($friends) > 0) $fw->setKnows($friends);

            $fw->display();
            return VIEW_NONE;
        }

        $request->setAttribute('member', $member);
        return VIEW_SUCCESS;
    }
}
?>
