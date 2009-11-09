<?php
class FavoritTagAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $site = DB_DataObject::factory('site');
        $site->query(
            'SELECT s.id, s.title, s.description, s.createdtime, m.account AS account' .
            ' FROM site s JOIN member m ON s.member_id = m.id'
        );
        $sites = array();
        while ($site->fetch()) {
            $sites[] = $site;
        }
        $request->setAttribute('sites', $sites);
        return VIEW_SUCCESS;
    }
}
?>
