<?php
require_once 'SiteUtils.php';
require_once 'EntryUtils.php';

class EntriesAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $site = SiteUtils::get_by_account($account);
        $site['link'] = SCRIPT_PATH."$account/";
        $site['uri'] = $site['link'].'rss';
        $site['rss_icon'] = SCRIPT_PATH.'images/feed_icon.gif';
        $request->setAttribute('site', $site);

        $entries = EntryUtils::get_entries_by_account($account, 5);
        foreach ($entries as $key => $value) {
            $entries[$key]['title'] = $this->mb_truncate($value['title']);
            $entries[$key]['link'] = SCRIPT_PATH."$account/{$value['id']}";
            $entries[$key]['favicon'] = 
            SCRIPT_PATH."icon.php?url={$value['favicon']}";
        }
        $request->setAttribute('entries', $entries);
        return VIEW_SUCCESS;
    }

    function mb_truncate($string, $length = 20, $etc = '...') {
        if (mb_strlen($string) > $length) {
            return mb_substr($string, 0, $length).$etc;
        } else {
            return $string;
        }
    }
}
?>
