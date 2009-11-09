<?php
require_once 'FoafUtils.php';
require_once 'LogUtils.php';

class FoafAction extends Action
{
    function validate(&$controller, &$request, &$user)
    {
/*
        $account = $request->hasParameter('account')
            ? $request->getParameter('account') : '';

        $url = $request->hasParameter('url')
            ? $request->getParameter('url') : '';

        if (empty($account) || empty($url)) {
            return false;
        }
*/
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $method = $request->hasParameter('_method') ? $request->getParameter('_method') : $_SERVER['REQUEST_METHOD'];
        switch (strtoupper($method)) {
        case 'POST':
            return $this->post($controller, $request, $user);
            break;
        case 'DELETE':
            return $this->delete($controller, $request, $user);
            break;
        case 'GET':
        default:
            return VIEW_NONE;
        }
    }

    function post(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $url = $request->getParameter('url');
        $member = $user->getAttribute('member', GLU_NS);

        LogUtils::debug("Added FOAF. account: $account, url: $url");

        if (@FoafUtils::is_exists_by_account_and_url($account, $url)) {
            echo 'exists';
            return VIEW_NONE;
        }

        $foaf = DB_DataObject::factory('foaf');
        $foaf->member_id = $member->id;
        $foaf->url = $url;

        $foaf_id = $foaf->insert();
        if ($foaf_id === false) {
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

    function delete(&$controller, &$request, &$user)
    {
        $account = $request->getParameter('account');
        $foaf_id = $request->hasParameter('foaf_id') ? $request->getParameter('foaf_id') : '';
        $member = $user->getAttribute('member', GLU_NS);

        LogUtils::debug("Deleted FOAF. account: $account, foaf_id: $foaf_id");

        $foaf = DB_DataObject::factory('foaf');
        $foaf->id = $foaf_id;
        $foaf->member_id = $member->id;

        $foaf_id = $foaf->delete();
        if ($foaf_id === false) {
            $error =& PEAR::getStaticProperty('DB_DataObject', 'lastError');
            if(PEAR::isError($error)){
                trigger_error($error->toString(), E_USER_ERROR);
            }
            echo 'false';
            return VIEW_NONE;
        }
        echo 'true';
        return VIEW_NONE;

    }
}
?>
