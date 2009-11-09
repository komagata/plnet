<?php
class UpdateAction extends Action
{
    var $layout = 'Admin';

    function initialize(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = "Plnet &gt; ".msg('setting')." &gt; ".msg('site');

        $member = $user->getAttribute('member', GLU_NS);
        $site = DB_DataObject::factory('site');
        $site->get('member_id', $member->id);
        $request->setAttribute('site', $site);
        $request->setAttribute('account', $member->account);
        $request->setAttribute('member', $member);

        $request->setAttribute('show_profiles', array(
            '1' => msg('show'),
            '0' => msg('hidden')
        ));
        $request->setAttribute('show_footprints', array(
            '1' => msg('show'),
            '0' => msg('hidden')
        ));
        return true;
    }

    function isSecure() { return true; }

    function validate (&$controller, &$request, &$user)
    {
        return true;
    }

    function registerValidators(&$validatorManager, &$controller, &$request, &$user)
    {
        $validatorManager->setRequired('title', true, msg('title is required'));
    }

    function getRequestMethods()
    {
        return REQ_POST;
    }

    function execute(&$controller, &$request, &$user)
    {
        $site = DB_DataObject::factory('site');
        $site->get('id', $request->getParameter('id'));
        $site->title = $request->getParameter('title');
        $site->description = $request->getParameter('description');
        $site->show_profile = $request->getParameter('show_profile');
        $site->show_footprint = $request->getParameter('show_footprint');
        $site_id = $site->update();
        if ($site_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if(PEAR::isError($error)){
                trigger_error($error->toString(), E_USER_ERROR);
                exit;
            }
            return VIEW_NONE;
        }
        Controller::redirect(SCRIPT_PATH . 'setting/site/changed');
        return VIEW_NONE;
    }
}
?>
