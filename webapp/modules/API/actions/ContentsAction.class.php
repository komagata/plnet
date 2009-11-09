<?php
require_once 'ContentUtils.php';

class ContentsAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $count = $request->hasParameter('count') ?
            $request->getParameter('count') : 256;
        $request->setAttribute('count', $count);

        $callback = $request->hasParameter('callback') ?
            $request->getParameter('callback') : false;
        $request->setAttribute('callback', $callback);

        $contents = ContentUtils::get_list($count);
        foreach ($contents as $i => $content)
            $contents[$i]['target_text'] = msg($content['target']);

        $request->setAttribute('contents', $contents);
        return VIEW_SUCCESS;
    }
}
?>
