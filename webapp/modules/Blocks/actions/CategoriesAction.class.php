<?php
require_once 'MemberUtils.php';
require_once 'ContentCategoryUtils.php';

class CategoriesAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $cc = ContentCategoryUtils::find();
        ContentCategoryUtils::array_localize($cc);
        $request->setAttribute('categories', $cc);
        return VIEW_SUCCESS;
    }
}
?>
