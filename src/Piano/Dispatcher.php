<?php
namespace Piano;

class Dispatcher
{
    protected $request;
    protected $router;
    protected $controllerClassNamespace;
    protected $beforeHooks = [];
    protected $afterHooks = [];

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
        foreach ($this->beforeHooks as $beforeHook) {
            if (is_callable($beforeHook)) {
                call_user_func_array($beforeHook, [$request]);
            }
        }

        $response = null;

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
            } catch (\Exception $e) {
                $response = $this->error($e);
            }
        } else {
            $response = $this->notfound();
        }

        foreach ($this->afterHooks as $afterHook) {
            if (is_callable($afterHook)) {
                call_user_func_array($afterHook, [$request, $response]);
            }
        }
        return $response;
    }

    public function before(callable $callback)
    {
        $this->beforeHooks[] = $callback;
    }

    public function after(callable $callback)
    {
        $this->afterHooks[] = $callback;
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
