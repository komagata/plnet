<?php
require_once 'OPMLWriter.php';

class SourcesAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');

        $format = $request->hasParameter('format')
            ? $request->getParameter('format') : 'html';


        $member = DB_DataObject::factory('member');
        $member->get('account', $account);

        $site = DB_DataObject::factory('site');
        $site->get('member_id', $member->id);

        $feed = DB_DataObject::factory('feed');
        $feeds = $feed->getListsByAccount($account);

        switch ($format) {
        case 'opml11' :

            foreach ($feeds as $key => $feed) {
                $f = array(
                    'title' => $feed->title,
                    'text' => $feed->description,
                    'link' => $feed->link,
                    'uri' => $feed->uri
                );
                $feeds[$key] = $f;
            }

            $writer =& new OPMLWriter();
            $writer->setHead(array(
                'title' => $site->title,
                'date' => time(),
                'owner' => $account
            ));
            $writer->setOutlines($feeds);
            $writer->display($format);
            return VIEW_NONE;
        case 'html' :
        default :
            $request->setAttribute('sources', $feeds);
            return VIEW_SUCCESS;
        }
    }
}
?>
