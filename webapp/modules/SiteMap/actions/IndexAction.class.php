<?php
require_once 'EntryUtils.php';

class IndexAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member = DB_DataObject::factory('member');
        $member->find();
        $sitemaps = array();
        while ($member->fetch()) {
            $last_update = EntryUtils::get_last_update_by_account($member->account); 
            $last_update = $last_update > 0 ? $last_update : time();
            $sitemaps[] = array(
                'loc' => SCRIPT_PATH . $member->account . '/sitemap',
                'lastmod' => $last_update
            );
        }
        $request->setAttribute('sitemaps', $sitemaps);
        return VIEW_SUCCESS;
    }
}
?>
