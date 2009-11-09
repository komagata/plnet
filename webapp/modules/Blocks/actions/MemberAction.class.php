<?php
require_once 'HTML/AJAX/JSON.php';

class MemberAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $member = $user->getAttribute('member', GLU_NS);
        unset($member->photo);
        Utils::remove_private_property(&$member);

        $haj = new HTML_AJAX_JSON;
        $m = $haj->encode($member);

        $output = "if (typeof 'Plnet' == 'undefined') Plnet = {}\n";
        $output .= "Plnet.Member = {$m}";
        header('Content-Type: text/javascript; charset=utf-8');
        echo $output;
        return VIEW_NONE;
    }
}
?>
