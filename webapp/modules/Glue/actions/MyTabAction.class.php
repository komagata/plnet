<?php
require_once 'HTML/AJAX/JSON.php';

class MyTabAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member_id = $request->getParameter('member_id');

        // my feeds
        $source = DB_DataObject::factory('source');
        $source->whereAdd('member_id = ' . $source->escape($member_id));
        $source->find();
        $feeds = array();
        while ($source->fetch()) {
            $feeds[] = array(
                'id' => $source->id,
                'title' => $source->name,
                'uri' => $source->uri
            );
        }

        $haj = new HTML_AJAX_JSON();
        $output = $haj->encode($feeds);

        header('Content-Type: application/x-javascript; charset=utf-8');
        echo $output;
        return VIEW_NONE;
    }
}
?>
