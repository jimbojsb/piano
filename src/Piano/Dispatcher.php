<?php
namespace Piano;

use \Piano\Exception,
    \Piano\Request,
    \Piano\Router\RouterInterface;

class Dispatcher
{
    protected $config;
    protected $routers = array();

    public function addRouter(RouterInterface $router)
    {
        $this->routers[] = $router;
    }

    public function dispatch(Request $request)
    {
        $controller = null;
        $action = null;
        foreach ($this->routers as $router) {
            if ($params = $router->match($request)) {
                $controller = $router->getParam('controller');
                $action = $route->getParam('action');
            }
        }

        if ($controller && $action) {
            $controllerNamespace = "\\" . $this->config->app->namespace . "\\Controller";

            $controllerName = ucfirst(strtolower($controller));
            $controllerClassName = "$controllerNamespace\\$controllerName";
            $controllerFilename = APP_PATH . "/app/controllers/$controllerName.php";

            if (file_exists($controllerFilename)) {
               require_once $controllerFilename;
               $controller = new $controllerClassName();
               $actionName = strtolower($action);
               if (method_exists($controller, $actionName . "Action")) {
                   $r = new \ReflectionMethod($controller, $actionName . "Action");
                   $actionParams = $r->getParameters();
                   $actionParamsToPass = array();
                   $params = $request->getParams();
                   foreach ($actionParams as $param) {
                   $param = $param->name;
                       if (isset($params[$param])) {
                           $actionParamsToPass[] = $params[$param];
                       }
                   }

                   $response = call_user_func_array(array($controller, $actionName . "Action"), $actionParamsToPass);
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

    public function loadRoutes($routesFile)
    {
        $routes = include $routesFile;
        if (!is_array($routes)) {
            throw new \Piano\Exception("no routes found in $routesFile");
        }
        foreach ($routes as $route => $params) {
            $this->addRoute(new StandardRoute($route, $params));
        }

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