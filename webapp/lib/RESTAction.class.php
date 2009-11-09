<?php
class RESTAction extends Action
{
    function execute(&$controller, &$request, &$user)
    {
        $method = $request->hasParameter('_method')
            ? $request->getParameter('_method') : $_SERVER['REQUEST_METHOD'];
        switch (strtoupper($method)) {
        case 'POST':
            return $this->post($controller, $request, $user);
            break;
        case 'PUT':
            return $this->put($controller, $request, $user);
            break;
        case 'DELETE':
            return $this->delete($controller, $request, $user);
            break;
        case 'GET':
        default:
            return $this->get($controller, $request, $user);
        }
    }
}
?>
