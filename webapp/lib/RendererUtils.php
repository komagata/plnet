<?php
require_once 'Smarty.class.php';
require_once dirname(BASE_DIR).'/mojavi/opt/renderers/SmartyRenderer.class.php';
require_once BASE_DIR.'opt/smarty/plugins/function.mojavi_url.php';
require_once BASE_DIR.'opt/smarty/plugins/function.mojavi_action.php';
require_once BASE_DIR.'opt/smarty/plugins/function.mojavi_error.php';
require_once BASE_DIR.'opt/smarty/plugins/function.css_link_tag.php';
require_once BASE_DIR.'opt/smarty/plugins/function.js_link_tag.php';
require_once BASE_DIR.'opt/smarty/plugins/function.to_json.php';
require_once BASE_DIR.'opt/smarty/plugins/function.html_select_date_simple.php';
require_once BASE_DIR.'opt/smarty/plugins/modifier.format_price.php';
require_once BASE_DIR.'opt/smarty/plugins/modifier.mb_truncate.php';
require_once BASE_DIR.'opt/smarty/plugins/modifier.tofavicon.php';
require_once BASE_DIR.'opt/smarty/plugins/modifier.date_RFC822.php';
require_once BASE_DIR.'opt/smarty/plugins/modifier.date_W3C.php';
require_once BASE_DIR.'opt/smarty/plugins/modifier.msg.php';

class RendererUtils
{
    function &getCachedSmartyRenderer($lifetime = 3600)
    {
        $renderer =& RendererUtils::getSmartyRenderer();
        $smarty =& $renderer->getEngine();
        $smarty->caching = 2;
        $smarty->cache_lifetime = $lifetime;
        return $renderer;
    }

    function &getSmartyRenderer()
    {
        global $messages;

        $controller =& Controller::getInstance();
        $request    =& $controller->request;
        $user       =& $controller->user;

        $renderer   =& new SmartyRenderer($controller, $request, $user);
        $smarty     =& $renderer->getEngine();

        $smarty->cache_dir      = SMARTY_CACHE_DIR;
        $smarty->caching        = SMARTY_CACHING;
        $smarty->force_compile  = SMARTY_FORCE_COMPILE;
        $smarty->compile_dir    = SMARTY_COMPILE_DIR;
        $smarty->config_dir     = $controller->getModuleDir() . 'config/';
        $smarty->debugging      = SMARTY_DEBUGGING;
        $smarty->compile_id     = $controller->currentModule .
            '_' . $controller->currentAction;

        $smarty->register_function('mojavi_url',
        'smarty_function_mojavi_url');
        $smarty->register_function('mojavi_action',
        'smarty_function_mojavi_action');
        $smarty->register_function('mojavi_error',
        'smarty_function_mojavi_error');
        $smarty->register_function('css_link_tag',
        'smarty_function_css_link_tag');
        $smarty->register_function('js_link_tag',
        'smarty_function_js_link_tag');
        $smarty->register_function('to_json',
        'smarty_function_to_json');
        $smarty->register_function('html_select_date_simple',
        'smarty_function_html_select_date_simple');
        $smarty->register_modifier('format_price',
        'smarty_modifier_format_price');
        $smarty->register_modifier('mb_truncate',
        'smarty_modifier_mb_truncate');
        $smarty->register_modifier('tofavicon',
        'smarty_modifier_tofavicon');
        $smarty->register_modifier('date_RFC822',
        'smarty_modifier_date_RFC822');
        $smarty->register_modifier('date_W3C',
        'smarty_modifier_date_W3C');
        $smarty->register_modifier('msg',
        'smarty_modifier_msg');

        if ($user->isAuthenticated() 
        && $user->hasAttribute('member', GLU_NS)) {
            $smarty->assign('member', $user->getAttribute('member', GLU_NS));
        }

        $smarty->assign($_SERVER);
        $smarty->assign('params', $request->getParameters());
        $smarty->assign('errors', $request->getErrors());
        $smarty->assign('module', $controller->requestModule);
        $smarty->assign('action', $controller->requestAction);
        $smarty->assign(array(
            'webmod' => WEB_MODULE_DIR,
            'curmod' => WEB_MODULE_DIR . $controller->currentModule . '/',
            'SCRIPT_PATH' => SCRIPT_PATH,
            'PLNET_CUSTOM_TEMPLATE_ID' => PLNET_CUSTOM_TEMPLATE_ID
        ));
        $smarty->assign('shared_dir', BASE_DIR.'templates');
        $smarty->assign($request->getAttributes());
        return $renderer;
    }
}
?>
