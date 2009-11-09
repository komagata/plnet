<?php
require_once 'FoafUtils.php';
require_once 'LogUtils.php';

class FoafsAction extends Action
{
    function validate(&$controller, &$request, &$user)
    {
        $account = $request->hasParameter('account')
            ? $request->getParameter('account') : '';

        if (empty($account)) {
            return false;
        }
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $raw = $request->hasParameter('raw') ? true : false;
        $request->setAttribute('raw', $raw);

        $callback = $request->hasParameter('callback') ?
            $request->getParameter('callback') : false;
        $request->setAttribute('callback', $callback);

        $member = $user->getAttribute('member', GLU_NS);

        $foaf = DB_DataObject::factory('foaf');
        $foaf->whereAdd('member_id = '.$foaf->escape($member->id));
        $foaf->find();

        $foafs = array();
        while ($foaf->fetch()) {
            $foafs[] = $foaf;
        }
        $request->setAttribute('foafs', $foafs);
        return VIEW_SUCCESS;
    }
}
?>
