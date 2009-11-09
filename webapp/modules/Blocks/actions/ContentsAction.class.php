<?php
class ContentsAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $content = DB_DataObject::factory('content');
        $content->orderBy('name');
        $content->find();
        $contens = array();
        while ($content->fetch()) {
            $contents[] = $content;
        }

        $request->setAttribute('contents', $contents);
        return VIEW_SUCCESS;
    }
}
?>
