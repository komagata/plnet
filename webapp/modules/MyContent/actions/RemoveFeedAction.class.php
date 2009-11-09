<?php
class RemoveFeedAction extends Action
{
    function isSecure()
    {
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $member = $user->getAttribute('member', GLU_NS);
        $feedId = $request->getParameter('id');

        $m2f = DB_DataObject::factory('member_to_feed');
        $m2f->member_id = $member->id;
        $m2f->feed_id = $feedId;
        if ($m2f->delete() === false) {
            header('Content-Type: plain/text; charset=utf-8');
            echo 'false';
            return VIEW_NONE;
        }

        $m2cc2f = DB_DataObject::factory('member_to_content_category_to_feed');
        $m2cc2f->member_id = $member->id;
        $m2cc2f->feed_id = $feedId;
        if ($m2cc2f->delete() === false) {
            header('Content-Type: plain/text; charset=utf-8');
            echo 'false';
            return VIEW_NONE;
        }

        header('Content-Type: plain/text; charset=utf-8');
        echo 'true';
        return VIEW_NONE;
    }
}
?>
