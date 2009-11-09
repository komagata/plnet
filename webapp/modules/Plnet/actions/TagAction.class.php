<?php
require_once 'EntryUtils.php';
require_once 'TagUtils.php';

class TagAction extends Action
{
    var $layout = 'Public';

    function execute(&$controller, &$request, &$user)
    {
        $this->attrs['title'] = 'Plnet &gt; '.msg('tags');

        $tag = $request->hasParameter('tag')
            ? $request->getParameter('tag') : null;

        if ($tag) {
            $entries = EntryUtils::get_entries_by_tagname($tag);

            foreach ($entries as $key => $entry) {
                $entry['tags'] = TagUtils::get_tags_by_entry_id($entry['id']);
            }

            $request->setAttribute('pager', ActionUtils::pager($entries));
            $request->setAttribute('tag', $tag);
        }
        return VIEW_INDEX;
    }
}
?>
