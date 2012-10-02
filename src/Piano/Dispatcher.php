<?php
namespace Piano;

use \Piano\Exception,
    \Piano\Request,
    \Piano\Router\AbstractRouter;

class Dispatcher
{
    protected $router;

    public function __construct(AbstractRouter $router)
    {
        $this->router = $router;
    }

    public function dispatch(Request $request)
    {
        $matchedRoute = $this->router->route($request);
        if ($matchedRoute) {
            $callback = $matchedRoute['callback'];
            $availableParams = $matchedRoute['params'];
            $paramsToPass = array();
            $requestedParams = array();
            if (!is_callable($callback)) {
                if (is_string($callback)) {
                    list($controllerClassName, $actionMethodName) = explode('.', $callback);
                    $controller = new $controllerClassName;
                    $callback = array($controller, $actionMethodName);
                    $r = new \ReflectionMethod($controller, $actionMethodName);
                    $methodParams = $r->getParameters();
                    foreach ($methodParams as $param) {
                        $requestedParams[] = $param->name;
                    }
                }
            } else {
                $r = new \ReflectionFunction($callback);
                $methodParams = $r->getParameters();
                foreach ($methodParams as $param) {
                    $requestedParams[] = $param->name;
                }
            }

            foreach ($requestedParams as $param) {
                $paramsToPass[] = $availableParams[$param];
            }
            $response = call_user_func_array($callback, $paramsToPass);
            return $response;
        } else {

        }
    }
}