<?php
class FeedbackAction extends Action
{
    function validate(&$controller, &$request, &$user)
    {
        $name = $request->hasParameter('name')
            ? $request->getParameter('name') : '';
        $comment = $request->hasParameter('comment')
            ? $request->getParameter('comment') : '';
        if (strlen($name) === 0 || strlen($comment) === 0) {
            return false;
        }
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $feedback = DB_DataObject::factory('feedback');
        $feedback->name = $request->getParameter('name');
        $feedback->comment = $request->getParameter('comment');
        $feedback_id = $feedback->insert();
        if ($feedback_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if(PEAR::isError($error)){
                trigger_error($error->toString(), E_USER_ERROR);
                exit;
            }
            return VIEW_NONE;
        }
        echo 'true';
        return VIEW_NONE;
    }
}
?>
