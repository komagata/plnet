<?php
if (!defined(CACHE_LITE_DIR)) define('CACHE_LITE_DIR', '/tmp/');
if (!defined(CACHE_LITE_AUTO_CLEANING)) define('CACHE_LITE_AUTO_CLEANING', 128);

function smarty_function_mojavi_action($params, &$smarty)
{
    $id = $params['module'].'_'.$params['action'];

    if (isset($params['lifetime'])) {
        include_once 'Cache/Lite.php';
        $cache =& new Cache_Lite(array(
            'cacheDir' => CACHE_LITE_DIR,
            'lifeTime' => $params['lifetime'],
            'automaticCleaningFactor' => CACHE_LITE_AUTO_CLEANING,
            'automaticSerialization' => true
        ));

        $cache_id = $id.'_'.
            (isset($params['cache_id']) ? $params['cache_id'] : '');
        if ($data = $cache->get($cache_id)) {
            return $data;
        }
    }

    $controller =& Controller::getInstance();
    $actionChain =& new ActionChain();
    $actionChain->register($id, $params['module'], $params['action']);
    $actionChain->execute($controller, $controller->request, $controller->user);
    $data = $actionChain->fetchResult($id);
    if (isset($params['lifetime'])) $cache->save($data, $cache_id);
    return $data;
}
?>
