<?php
class ListAction extends Action
{
    var $layout = 'Public';

    function execute(&$controller, &$request, &$user)
    {
        $content_category = DB_DataObject::factory('content_category');
        $content_category->find();
        $content_categories = array();
        while ($content_category->fetch()) {
            $content = DB_DataObject::factory('content');
            $content->content_category_id = $content_category->id;
            $content->find();
            $contents = array();
            while ($content->fetch()) {
                $contents[] = array(
                    'id' => $content->id,
                    'uri' => $content->uri,
                    'icon' => $content->icon,
                    'format' => $content->format,
                    'description' => $content->description,
                    'name' => $content->name
                );
            }

            $content_categories[] = array(
                'id' => $content_category->id,
                'name' => $content_category->name,
                'contents' => $contents
            );

        }
        $request->setAttribute('content_categories', $content_categories);
        return VIEW_SUCCESS;
    }
}
?>
