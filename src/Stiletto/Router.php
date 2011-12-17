<?php
namespace Stiletto;
class Router
{
    protected $routes;
    protected $app;

    public function __construct($routesFile)
    {
        if (file_exists($routesFile)) {
            $routes = include $routesFile;
        } else {
            throw new \Exception($routesFile . " not found");
        }

        foreach ($routes as $route => $callbackFile) {
            list($method, $path) = explode(" ", $route);

            if (strpos($method, ',') !== false) {
                $method = explode(',', $method);
            }
            $routeData = array("method" => $method,
                               "callbackFile" => $callbackFile);
            $this->routes[$path] = $routeData;
        }
    }

    public function route()
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $urlParts = @parse_url($_SERVER['REQUEST_URI']);
        $path = $urlParts['path'];


        foreach ($this->routes as $path => $routeData) {
            if ($path == $path) {
                if ($method = $routeData['method']) {
                    $callback = include APP_PATH . '/controllers/' . $routeData['callbackFile'];
                    if (is_callable($callback)) {
                        return $callback;
                    } else {
                        throw new \Exception('Result of including ' . $routeData['callbackFile']  . ' was not callable');
                    }
                }
            }
        }


    }
}