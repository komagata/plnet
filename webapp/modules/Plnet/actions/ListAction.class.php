<?php
require_once 'FeedUtils.php';
require_once 'FeedWriter.php';

class ListAction extends Action
{
    var $layout = 'Public';

    function execute(&$controller, &$request, &$user)
    {
        $format = $request->hasParameter('format')
            ? $request->getParameter('format') : 'html';

        $limit = $format != 'html' ? PLNET_FEED_NUMBER : null;

        $this->attrs['title'] = 'Plnet &gt; '.msg('plnet list');

        $member = DB_DataObject::factory('member');
        $sql = 'SELECT m.id, m.account, m.photo,
            UNIX_TIMESTAMP(m.createdtime) AS createdtime,
            s.title, s.description
            FROM member m
            JOIN site s ON m.id = s.member_id
            ORDER BY createdtime DESC';
        if ($limit) $sql .= " LIMIT $limit";

        $member->query($sql);
        $members = array();
        while ($member->fetch()) {
            $feeds = FeedUtils::get_feeds_by_account($member->account);
            $m = array(
                'account'      => $member->account,
                'profile_icon' => $member->photo
                    ? SCRIPT_PATH."photo.php?member_id={$member->id}"
                    : SCRIPT_PATH.'images/profile_icon.jpg',
                'author'       => $member->account,
                'title'        => $member->title,
                'link'         => SCRIPT_PATH."{$member->account}/",
                'date'         => $member->createdtime,
                'description'  => $member->description,
                'feeds'        => $feeds,
            );
            $members[] = $m;
        }

        $channel = array(
           'title' => '新しいPlnet',
           'link' => SCRIPT_PATH.'list/',
           'description' => '新しいPlnet'
        );

        switch ($format) {
        case 'rss10' :
            $writer =& new FeedWriter();
            $channel['uri'] = "{$channel['link']}rss";
            $writer->setChannel($channel);
            $writer->setItems($members);
            $writer->display($format);
            return VIEW_NONE;
        case 'rss20' :
            $writer =& new FeedWriter();
            $channel['uri'] = "{$channel['link']}rss2";
            $writer->setChannel($channel);
            $writer->setItems($members);
            $writer->display($format);
            return VIEW_NONE;
        case 'html' :
        default :
            $request->setAttribute('pager', ActionUtils::pager($members, 50));
            return VIEW_INDEX;
        }
    }
}
?>
