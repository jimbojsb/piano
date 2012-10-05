<?php
namespace Piano;

class Dispatcher
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function dispatch(Request $request)
    {
        $matchedRoute = $this->application->router->route($request);
        if ($matchedRoute) {
            $callback = $matchedRoute['callback'];
            $availableParams = $matchedRoute['params'];
            $paramsToPass = array();
            $requestedParams = array();
            if (!is_callable($callback)) {
                if (is_string($callback)) {
                    list($controllerClassName, $actionMethodName) = explode('.', $callback);
                    $controller = new $controllerClassName;
                    if(method_exists($controller, 'setApplication')) {
                        $controller->setApplication($this->application);
                    }
                    if(method_exists($controller, 'setRequest')) {
                        $controller->setRequest($request);
                    }
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
            if (!$response instanceof Response) {
                $response = new Response($response);
            }
            return $response;
        } else {

        }
    }
}