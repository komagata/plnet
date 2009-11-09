<?php
require_once 'FeedWriter.php';

class FeedbacksAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $page = $request->hasParameter('page')
            ? $request->getParameter('page') : 1;
        $format = $request->hasParameter('format')
            ? $request->getParameter('format') : 'json';

        $perPage = 10;
        $offset = ($page-1) * $perPage;

        $feedback = DB_DataObject::factory('feedback');
        $count = $feedback->count();
        $feedbacks = $feedback->getList($perPage, $offset, array('id' => 'DESC'));

        $channel = array(
           'title' => msg('feedback'),
           'link' => SCRIPT_PATH.'feedback',
           'description' => msg('feedback from user'),
           'date' => strtotime($feedbacks[0]->created_at)
        );

        $fbs = array();
        foreach ($feedbacks as $key => $fb) {
            $fbs[] = array(
                'title' => sprintf(msg('feedback from %s'), $fb->name),
                'link' => $channel['link'],
                'description' => $fb->comment,
                'author' => $fb->name,
                'date' => strtotime($fb->created_at),
            );
        }

        switch ($format) {
        case 'rss10':
            $fbs = array_slice($fbs, 0, PLNET_FEED_NUMBER);
            $writer =& new FeedWriter();
            $channel['uri'] = "{$channel['link']}rss";
            $writer->setChannel($channel);
            $writer->setItems($fbs);
            $writer->display($format);
            break;
        case 'rss20':
            $fbs = array_slice($fbs, 0, PLNET_FEED_NUMBER);
            $writer =& new FeedWriter();
            $channel['uri'] = "{$channel['link']}rss2";
            $writer->setChannel($channel);
            $writer->setItems($fbs);
            $writer->display($format);
            break;
        case 'json':
        default:
            header('Content-Type: text/javascript; charset=utf-8');
            echo Utils::to_json(array(
                'page' => $page,
                'perPage' => $perPage,
                'total' => $count,
                'data' => $feedbacks
            ));
        }

        return VIEW_NONE;
    }
}
?>
