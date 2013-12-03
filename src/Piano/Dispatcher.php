<?php
namespace Piano;

class Dispatcher
{
    protected $request;
    protected $router;
    protected $controllerClassNamespace;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function setControllerClassNamespace($controllerClassNamespace)
    {
        $this->controllerClassNamespace = $controllerClassNamespace;
    }

    public function dispatch(Request $request)
    {
        $this->request = $request;
        $paramsToPass = array();
        $matchedRoute = $this->router->route($request);
        if ($matchedRoute) {
            $callback = $matchedRoute['callback'];
            $availableParams = $matchedRoute['params'];
            $availableParams['_request'] = $request;
            $requestedParams = array();
            $dispatchable = $this->getDispatchable($callback);

            $r = new \ReflectionMethod($dispatchable[0], $dispatchable[1]);
            $methodParams = $r->getParameters();
            foreach ($methodParams as $param) {
                $requestedParams[] = $param->name;
            }

            foreach ($requestedParams as $param) {
                $paramsToPass[] = $availableParams[$param];
            }

            try {
                $response = $this->execute($dispatchable, $paramsToPass);
                return $response;
            } catch (\Exception $e) {
                return $this->error($e);
            }
        } else {
            return $this->notfound();
        }
    }

    public function execute($dispatchable, $params = [])
    {
        $response = call_user_func_array($dispatchable, $params);
        if (!$response instanceof Response) {
            $response = new Response($response);
        }
        return $response;
    }

    public function error(\Exception $e = null)
    {
        $response = new Response;
        $errorHandler = $this->router->getErrorHandler();
        if ($errorHandler) {
            $callback = $errorHandler->getCallback();
            $error = $this->getDispatchable($callback);
            if ($error) {
                $response = $this->execute($error, [$e]);
                if (!$response instanceof Response) {
                    $response = new Response($response);
                }
            }
        }

        $response->setStatusCode(500);
        return $response;
    }

    public function notfound()
    {
        $response = new Response;

        $notfoundHandler = $this->router->getNotfoundHandler();
        if ($notfoundHandler) {
            $callback = $notfoundHandler->getCallback();
            $notfound = $this->getDispatchable($callback);
            if ($notfound) {
                $response = $this->execute($notfound);
            }
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
            if ($this->controllerClassNamespace) {
                $controllerClassName = $this->controllerClassNamespace . '\\' . $controllerClassName;
            }
            $controller = new $controllerClassName;
            $callback = [$controller, $actionMethodName];
            return $callback;
        }
    }
}
