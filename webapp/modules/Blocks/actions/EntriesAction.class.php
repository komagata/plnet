<?php
require_once 'MemberUtils.php';
require_once 'EntryUtils.php';
require_once 'TagUtils.php';
require_once 'SiteUtils.php';
require_once 'FeedUtils.php';
require_once 'FeedWriter.php';

class EntriesAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $year = $request->hasParameter('year')
            ? $request->getParameter('year') : null;
        $month = $request->hasParameter('month')
            ? $request->getParameter('month') : null;
        $q     = $request->hasParameter('q')
            ? $request->getParameter('q') : null;
        $tag = $request->hasParameter('tag')
            ? $request->getParameter('tag') : null;
        $source_id = $request->hasParameter('source_id')
            ? $request->getParameter('source_id') : null;
        $category_id = $request->hasParameter('category_id')
            ? $request->getParameter('category_id') : null;
        $format = $request->hasParameter('format')
            ? $request->getParameter('format') : 'html';

        $page = $request->hasParameter('page') ? $request->getParameter('page') : 1;
        $per_page = $format == 'html' ? PLNET_ENTRIES_PER_PAGE : PLNET_FEED_NUMBER;
        $start = ($page-1) * PLNET_ENTRIES_PER_PAGE;

        $channel = array();
        $site = SiteUtils::get_by_account($account);
        $member = MemberUtils::get_by_account($account);

        switch (true) {
        case ($year && $month) :
            $t = "Archive: {$year}年{$month}月";
            $channel['title'] = $site['title']." $t";
            $channel['link'] = SCRIPT_PATH."$account/$year/$month/";
            $channel['description'] = $site['description'];
            $rss1 = "{$channel['link']}rss";

            $request->setAttribute('entries_title',
            "$t <a href=\"{$rss1}\"><img class=\"favicon\" src=\"images/feed_icon.gif\" /></a>");
            $entries = EntryUtils::get_entries_by_account_and_year_month($account, $year, $month, $per_page, $start);
            $entries_count = EntryUtils::get_entries_count_by_account_and_year_month($account, $year, $month);
            $path = "/$account/$year/$month/";
            break;
        case ($q) :
            $t = "Search: $q";
            $channel['title'] = $site['title']." $t";
            $channel['link'] = SCRIPT_PATH."$account/search/$q";
            $channel['description'] = $site['description'];
            $rss1 = "{$channel['link']}rss";

            $request->setAttribute('entries_title', $t.
            " <a href=\"{$rss1}\"><img class=\"favicon\" ".
            "src=\"images/feed_icon.gif\" /></a>");
            $entries = EntryUtils::get_entries_by_account_and_query($account, $q, $per_page, $start);
            $entries_count = EntryUtils::get_entries_count_by_account_and_query($account, $q);
            $path = "/$account/search/$q";
            break;
        case ($tag) :
            $t = "Tag: $tag";
            $channel['title'] = $site['title']." $t";
            $channel['link'] = SCRIPT_PATH."$account/tag/$tag";
            $channel['description'] = $site['description'];
            $rss1 = "{$channel['link']}rss";

            $request->setAttribute(
                'entries_title',
                "Tag: {$tag} <a href=\"{$SCRIPT_PATH}tag/{$tag}\" ".
                "title=\"Plnet Tag: {$tag}\">[all]</a> <a href=\"{$rss1}\">".
                "<img class=\"favicon\" src=\"images/feed_icon.gif\" /></a> "
            );
            $entries = EntryUtils::get_entries_by_account_and_tagname($account, $tag, $per_page, $start);
            $entries_count = EntryUtils::get_entries_count_by_account_and_tagname($account, $tag);
            $path = "/$account/tag/$tag";
            break;
        case ($source_id) :
            $feed = FeedUtils::get_feed_by_id($source_id);

            $t = "Source: {$feed['title']}";
            $channel['title'] = $site['title']." $t";
            $request->setAttribute('feed_title', $feed['title']);
            $channel['link'] = SCRIPT_PATH."$account/source/$source_id/";
            $channel['description'] = $site['description'];
            $rss1 = "{$channel['link']}rss";

            $entries_title = "Source: <a href=\"{$feed['link']} \">". 
            "{$feed['title']}</a> <a href=\"{$rss1}\"><img class=\"favicon\" ".
            "src=\"images/feed_icon.gif\" /></a>";
            $request->setAttribute('entries_title', $entries_title);
            $entries = EntryUtils::get_entries_by_account_feed_id($account, $source_id, $per_page, $start);
            $entries_count = EntryUtils::get_entries_count_by_account_feed_id($account, $source_id);
            $path = "/$account/source/$source_id";
            break;
        case ($category_id) :
            $content_category = ContentCategoryUtils::get($category_id);
            $entries_title = msg('category').": ".msg($content_category['name']);
            $request->setAttribute('entries_title', $entries_title);
            $entries = EntryUtils::find_by_member_id_and_category_id($member['id'], $category_id, $per_page, $start);
            $entries_count = EntryUtils::find_count_by_member_id_and_category_id($member['id'], $category_id);
            $path = "/$account/category/$category_id";
            break;
        default :
            $channel['title'] = $site['title'];
            $channel['link'] = SCRIPT_PATH."$account/";
            $channel['description'] = $site['description'];
            $entries = EntryUtils::get_entries_by_account($account, $per_page, $start);
            $entries_count = EntryUtils::get_entries_count_by_account($account);
            $path = "/$account/";
            break;
        }

        // tags
        foreach ($entries as $key => $entry) {
            $entries[$key]['link'] = SCRIPT_PATH."$account/{$entry['id']}";
            $entries[$key]['formated_date'] = date(msg('entry date format'), $entry['date']);
            $tags = TagUtils::get_tags_by_entry_id($entry['id']);
            if (count($tags) > 0) {
                foreach ($tags as $tag) {
                    $entries[$key]['tags'][] = $tag['name'];
                }
            }
        }

        switch ($format) {
        case 'rss10':
            $channel['uri'] = $channel['link'].'rss';
            $writer =& new FeedWriter();
            $writer->setChannel($channel);
            $writer->setItems($entries);
            $writer->display($format);
            return VIEW_NONE;
        case 'rss20':
            $channel['uri'] = $channel['link'].'rss2';
            $writer =& new FeedWriter();
            $writer->setChannel($channel);
            $writer->setItems($entries);
            $writer->display($format);
            return VIEW_NONE;
        case 'html':
        default:
            $request->setAttribute('entries', $entries);
            $request->setAttribute('pager', Utils::pager($page, $entries_count, $path));
            return VIEW_SUCCESS;
        }
    }
}
?>
