<?php
namespace Piano\Dispatcher;

use Piano\Request,
    Piano\Response;

class ActionController
{
    protected $controllerNamespace;

    public function __construct($appNamespace)
    {
        $controllerNamespace = "\\$appNamespace\\Controller";
        $this->controllerNamespace = $controllerNamespace;
    }

    public function dispatch(Request $request)
    {
        $controllerName = ucfirst(strtolower($request->getController()));
        $controllerClassName = "$this->controllerNamespace\\$controllerName";
        $controllerFilename = APP_PATH . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . $controllerName . ".php";

        if (file_exists($controllerFilename)) {
            require_once $controllerFilename;
            $controller = new $controllerClassName();
            $actionName = strtolower($request->getAction('action')) . "Action";
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

                $actionResult = call_user_func_array(array($controller, $actionName), $actionParamsToPass) ?: array();
                return $actionResult ?: array();
            }
        } else {
            throw new \Exception();
        }
    }
}