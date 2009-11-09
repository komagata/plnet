<?php
class MemberAction extends Action
{
    function validate(&$controller, &$request, &$user)
    {
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        switch ($_SERVER['REQUEST_METHOD']) {
        case 'DELETE':
          echo 'DELETE!!!';
          break;
        case 'GET':
        default:

        }

        return VIEW_NONE;
    }
}
?>
