<?php
namespace Piano;

class Dispatcher
{
    protected $application;
    protected $request;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function dispatch(Request $request)
    {
        $this->request = $request;
        $paramsToPass = array();
        $matchedRoute = $this->application->router->route($request);
        if ($matchedRoute) {
            $callback = $matchedRoute['callback'];
            $availableParams = $matchedRoute['params'];
            $requestedParams = array();
            $dispatchable = $this->getDispatchable($callback);

            if ($dispatchable instanceof \Closure) {
                $r = new \ReflectionFunction($callback);
                $methodParams = $r->getParameters();
                foreach ($methodParams as $param) {
                    $requestedParams[] = $param->name;
                }
            } else {
                $r = new \ReflectionMethod($dispatchable[0], $dispatchable[1]);
                $methodParams = $r->getParameters();
                foreach ($methodParams as $param) {
                    $requestedParams[] = $param->name;
                }
            }

            foreach ($requestedParams as $param) {
                $paramsToPass[] = $availableParams[$param];
            }

            try {
                $response = call_user_func_array($dispatchable, $paramsToPass);
                if (!$response instanceof Response) {
                    $response = new Response($response);
                }
                return $response;
            } catch (\Exception $e) {
                $paramsToPass["exception"] = $e;
                $error = $this->getDispatchable($this->application->router->error()->getCallback());
                $response = call_user_func_array($error, $paramsToPass);
                if (!$response instanceof Response) {
                    $response = new Response($response);
                }
                $response->setStatusCode(500);
                return $response;
            }
        } else {
            $notfound = $this->getDispatchable($this->application->router->notfound()->getCallback());
            $response = call_user_func_array($notfound, $paramsToPass);
            if (!$response instanceof Response) {
                $response = new Response($response);
            }
            $response->setStatusCode(404);
            return $response;
        }
    }

    private function getDispatchable($callback)
    {
        if (is_callable($callback)) {
            return $callback;
        } else if (is_string($callback)) {
            list($controllerClassName, $actionMethodName) = explode('.', $callback);
            $controller = new $controllerClassName;
            if(method_exists($controller, 'setApplication')) {
                $controller->setApplication($this->application);
            }
            if(method_exists($controller, 'setRequest')) {
                $controller->setRequest($this->request);
            }
            $callback = array($controller, $actionMethodName);
            return $callback;
        }
    }
}