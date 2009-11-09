<?php
require_once 'MemberUtils.php';
require_once 'FootprintUtils.php';

class AddFootprintAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->hasParameter('account') 
            ? $request->getParameter('account') : null;
        $member = $user->getAttribute('member', GLU_NS);

        $owner_id = MemberUtils::get_id_by_account($account);
        $visitor_id = isset($member->id) ? $member->id : null;

        if ($visitor_id and $owner_id != $visitor_id
        and !FootprintUtils::is_exist_today($owner_id, $visitor_id)) {
            $footprint = DB_DataObject::factory('footprint');
            $footprint->owner_id = $owner_id;
            $footprint->visitor_id = $visitor_id;
            $footprint_id = $footprint->insert();
            if ($footprint_id === false) {
                $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
                if(PEAR::isError($error)){
                    trigger_error($error->toString(), E_USER_ERROR);
                    exit;
                }
                return VIEW_NONE;
            }
        }
    }
}
?>
