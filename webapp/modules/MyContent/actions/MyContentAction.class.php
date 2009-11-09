<?php
define('GLUE_CONTENTNAME_LENGTH_MAX', 32);

require_once 'HTML/AJAX/JSON.php';

class MyContentAction extends Action
{
    function isSecure()
    {
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $member = $user->getAttribute('member', GLU_NS);

        // my contents
        $source = DB_DataObject::factory('source');
        $source->whereAdd('member_id = ' . $source->escape($member->id));
        $source->find();
        $my_contents = array();
        while ($source->fetch()) {
            
            if (mb_strlen($source->name) > GLUE_CONTENTNAME_LENGTH_MAX) {
                $source->name = mb_substr($source->name, 0, GLUE_CONTENTNAME_LENGTH_MAX) . '...';
            }
            $c = array(
                'id' => $source->id,
                'name' => $source->name,
                'uri' => $source->uri,
                'link' => $source->link,
                'icon' => $source->icon
            );
            $my_contents[] = $c;
        }

        $haj = new HTML_AJAX_JSON();
        $output = $haj->encode($my_contents);

        header('Content-Type: application/x-javascript; charset=utf-8');
        echo $output;
        return VIEW_NONE;
    }
}
?>
