<?php
require_once 'FootprintUtils.php';

class ListAction extends Action
{
    var $layout = 'Admin';

    function isSecure() { return true; }

    function execute(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = "Plnet &gt; ".msg('setting')." &gt; ".msg('footprint');

        $member = $user->getAttribute('member', GLU_NS);
        $owner_id = isset($member->id) ? $member->id : null;
        $fps = FootprintUtils::find_by_owner_id($owner_id);
        $footprints = array();
        foreach ($fps as $fp) {
            $fp['profile_icon'] = $fp['photo']
                ? SCRIPT_PATH."photo.php?member_id={$fp['id']}"
                : SCRIPT_PATH.'images/profile_icon.jpg';
            $fp['name'] = $fp['nickname'] ? $fp['nickname'] : $fp['account'];
            $fp['link'] = SCRIPT_PATH.$fp['account'].'/';
            $fp['formated_time'] = date(
              msg('entry date format'), 
              $fp['created_on']
            );
            $footprints[] = $fp;
        }

        $request->setAttribute('pager', ActionUtils::pager($footprints, 50));
        return VIEW_SUCCESS;
    }
}
?>
