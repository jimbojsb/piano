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
                return $this->error($e);
            }
        } else {
            return $this->notfound();
        }
    }

    public function error(Exception $e = null)
    {
        $error = $this->getDispatchable($this->application->router->error()->getCallback());
        if ($error) {
            $response = call_user_func_array($error, [$e]);
            if (!$response instanceof Response) {
                $response = new Response($response);
            }
        } else {
            $response = new Response;
        }

        $response->setStatusCode(500);
        return $response;
    }

    public function notfound()
    {
        $notfound = $this->getDispatchable($this->application->router->notfound()->getCallback());
        if ($notfound) {
            $response = call_user_func($notfound);
            if (!$response instanceof Response) {
                $response = new Response($response);
            }
        } else {
            $response = new Response;
        }
        $response->setStatusCode(404);
        return $response;
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