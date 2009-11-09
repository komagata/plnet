<?php
require_once 'MemberUtils.php';
require_once 'FeedUtils.php';

class UserListByContentCategoryAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $cc_name = $request->getAttribute('cc_name');
        $title = sprintf(msg('user list of this content category service'),
	                 msg($cc_name));
        $ms = MemberUtils::get_list_by_content_category_name($cc_name);

        foreach ((array)$ms as $member) {
            if ($member['show_profile']) {
                $feeds = FeedUtils::get_feeds_by_account($member['account']);
                $m = array(
                    'account'      => $member['account'],
                    'profile_icon' => $member['photo']
                        ? SCRIPT_PATH."photo.php?member_id={$member['id']}"
                        : SCRIPT_PATH.'images/profile_icon.jpg',
                    'self_introduction' =>$member['self_introduction'],
                    'feeds'        => $feeds,
                );
                $members[] = $m;
            }
        }

	$request->setAttribute('title', $title);
        $request->setAttribute('pager', ActionUtils::pager($members, 50));
        return VIEW_SUCCESS;
    }
}
?>
