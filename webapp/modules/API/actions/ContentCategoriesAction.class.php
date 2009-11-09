<?php
require_once 'ContentCategoryUtils.php';

class ContentCategoriesAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $callback = $request->hasParameter('callback') ?
            $request->getParameter('callback') : false;
        $request->setAttribute('callback', $callback);

        $content_categories = ContentCategoryUtils::find();
        foreach ($content_categories as $key => $content_category)
            $content_categories[$key]['name'] = msg($content_category['name']);

        $request->setAttribute(
            'content_categories',
            Utils::to_json($content_categories)
        );
        return VIEW_SUCCESS;
    }
}
?>
