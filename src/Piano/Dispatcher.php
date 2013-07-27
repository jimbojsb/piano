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
		$error = $this->getDispatchable($this->router->getErrorHandler()->getCallback());
        if ($error) {
            $response = $this->execute($error, [$e]);
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
		$notfound = $this->getDispatchable($this->router->getNotfoundHandler()->getCallback());
        if ($notfound) {
            $response = $this->execute($notfound);
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
            if ($this->controllerClassNamespace) {
                $controllerClassName = $this->controllerClassNamespace . '\\' . $controllerClassName;
            }
            $controller = new $controllerClassName;
            $callback = [$controller, $actionMethodName];
            return $callback;
        }
    }
}
