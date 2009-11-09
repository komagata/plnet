<?php
require_once 'TagCloud.class.php';
require_once 'TagUtils.php';

class HotTagsAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $tags = TagUtils::get_hot_tags(20);

        $cloud = new TagCloud();
        foreach ($tags as $tag) {
            $cloud->add(
                $tag['name'],
                SCRIPT_PATH . 'tag/' . urlencode($tag['name']),
                $tag['cnt']
            );
        }

        $request->setAttribute('tag_cloud', $cloud->htmlAndCSS());
        return VIEW_SUCCESS;
    }
}
?>
