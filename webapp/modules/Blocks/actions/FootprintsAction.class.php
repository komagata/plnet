<?php
require_once 'MemberUtils.php';
require_once 'FootprintUtils.php';
require_once 'SiteUtils.php';

class FootprintsAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->hasParameter('account') 
            ? $request->getParameter('account') : null;
        $member = $user->getAttribute('member', GLU_NS);

        $owner_id = MemberUtils::get_id_by_account($account);
        $visitor_id = isset($member->id) ? $member->id : null;

        $site = SiteUtils::get_by_account($account);
        $request->setAttribute('site', $site);

        $controller->forward('Footprint', 'AddFootprint');

        $footprints = FootprintUtils::get_footprints($owner_id, $visitor_id);
        $request->setAttribute('footprints', $footprints);

        return VIEW_SUCCESS;
    }
}
?>
