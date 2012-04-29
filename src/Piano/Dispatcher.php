<?php
namespace Piano;

class Dispatcher
{
    protected $config;
    protected $routes = [];

    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    public function dispatch(Request $request)
    {
        $controller = null;
        $action = null;
        foreach ($this->routes as $route) {
            if ($route->match($request)) {
                $controller = $route->getController();
                $action = $route->getAction();
            }
        }

        if ($controller && $action) {
            $controllerNamespace = "\\";
            if ($this->config->namespace) {
                $controllerNamespace .= $this->config->namespace;
                $controllerNamespace .= $this->config->namespace->controller ?: '\\Controller';
            }

            $controllerName = ucfirst(strtolower($controller));
            $controllerClassName = "$controllerNamespace\\$controllerName";
            $controllerFilename = APP_PATH . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . $controllerName . ".php";

            if (file_exists($controllerFilename)) {
               require_once $controllerFilename;
               $controller = new $controllerClassName();
               $actionName = strtolower($action);
               if (method_exists($controller, $actionName)) {
                   $r = new \ReflectionMethod($controller, $actionName);
                   $actionParams = $r->getParameters();
                   $actionParamsToPass = array();
                   $params = $request->getParams();
                   foreach ($actionParams as $param) {
                   $param = $param->name;
                       if (isset($params[$param])) {
                           $actionParamsToPass[] = $params[$param];
                       }
                   }

                   $response = call_user_func_array(array($controller, $actionName), $actionParamsToPass);
               }
            } else {
               throw new Exception(Exception::MESSAGE_ROUTE_NO_CONTROLLER, Exception::CODE_ROUTE_NO_CONTROLLER);
            }
        } else {
            throw new Exception(Exception::MESSAGE_ROUTE_NO_MATCH, Exception::CODE_ROUTE_NO_MATCH);
        }

        $request->setDispatched(true);
        return $response;

    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

}