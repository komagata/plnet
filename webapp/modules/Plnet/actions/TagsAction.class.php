<?php
require_once 'TagCloud.class.php';
require_once 'TagUtils.php';

class TagsAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $tags = TagUtils::get_all();
        $cloud = new TagCloud();
        foreach ($tags as $tag) {
            $cloud->add(
                $tag['name'], 
                SCRIPT_PATH.$account.'tag/'.urlencode($tag['name']) . '/', 
                $tag['cnt']
            );
        }

        $request->setAttribute('tag_cloud', $cloud->htmlAndCSS());
        return VIEW_INDEX;
    }
}
?>
