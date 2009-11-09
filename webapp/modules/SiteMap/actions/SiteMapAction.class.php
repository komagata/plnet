<?php
require_once'EntryUtils.php';
require_once'FeedUtils.php';
require_once'TagUtils.php';

class SiteMapAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {

        $account = $request->getParameter('account');

        $urls = array();

        $last_update = EntryUtils::get_last_update($account);
        $last_update = $last_update > 0 ? $last_update : time();

        // top
        $urls[] = array(
            'loc' => SCRIPT_PATH . $account . '/',
            'lastmod' => $last_update,
            'changefreq' => PLNET_SITEMAP_TOP_FREQ,
            'priority' => PLNET_SITEMAP_TOP_PRIORITY
        );

        // archive
        $archives = EntryUtils::get_archives_by_account($account);
        foreach ($archives as $archive) {
            $last_update = EntryUtils::get_last_update_by_account_and_year_month($account, $archive['y'], $archive['m']);
            $last_update = $last_update > 0 ? $last_update : time();
            $urls[] = array(
                'loc' => SCRIPT_PATH . "{$account}/{$archive['y']}/{$archive['m']}/",
                'lastmod' => $last_update,
                'changefreq' => PLNET_SITEMAP_ARCHIVE_FREQ,
                'priority' => PLNET_SITEMAP_ARCHIVE_PRIORITY
            );
        }
/*
        // tag
        $tags = TagUtils::get_tags_by_account($account);
        foreach ($tags as $tag) {
            $last_update = EntryUtils::get_last_update_by_account_and_tagname($account, $tag['id']);
            $last_update = $last_update > 0 ? $last_update : time();
            $urls[] = array(
                'loc' => SCRIPT_PATH . "{$account}/tag/{$tag['name']}/",
                'lastmod' => $last_update,
                'changefreq' => PLNET_SITEMAP_TAG_FREQ,
                'priority' => PLNET_SITEMAP_TAG_PRIORITY
            );
        }
*/
        // source
        $sources = FeedUtils::get_feeds_by_account($account);
        foreach ($sources as $source) {
            $last_update = EntryUtils::get_last_update_by_account_feed_id($account, $source['id']);
            $last_update = $last_update > 0 ? $last_update : time();
            $urls[] = array(
                'loc' => SCRIPT_PATH . $account . '/source/' . $source['id'],
                'lastmod' => $last_update,
                'changefreq' => PLNET_SITEMAP_SOURCE_FREQ,
                'priority' => PLNET_SITEMAP_SOURCE_PRIORITY
            );
        }

        // individual
        $entries = EntryUtils::get_entries_by_account($account);
        foreach ($entries as $entry) {
            $last_update = $entry['lastupdatedtime'];
            $last_update = $last_update > 0 ? $last_update : time();
            $urls[] = array(
                'loc' => SCRIPT_PATH . "{$account}/{$entry['id']}",
                'lastmod' => $last_update,
                'changefreq' => PLNET_SITEMAP_INDIVIDUAL_FREQ,
                'priority' => PLNET_SITEMAP_INDIVIDUAL_PRIORITY
            );
        }

        $request->setAttribute('urls', $urls);

        return VIEW_SUCCESS;
    }
}
?>
