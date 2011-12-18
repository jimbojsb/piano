<?php
namespace Stiletto;
class Router
{
    protected $routes;

    public function addRoute($route, $callback)
    {
        list($method, $path) = explode(" ", $route);

        if (strpos($method, ',') !== false) {
            $method = explode(',', $method);
        }

        if (!is_callable($callback)) {
            if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . $callback)) {
                $callback = include_once APP_PATH . DIRECTORY_SEPARATOR . $callback;
                if (!is_callable($callback)) {
                    throw new \Exception('No valid callback could be found for ' . $route);
                }
            }
        }

        $routeData = array("method" => $method,
                           "callback" => $callback);
        $this->routes[$path] = $routeData;
    }

    public function route()
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $urlParts = @parse_url($_SERVER['REQUEST_URI']);
        $requestPath = $urlParts['path'];

        foreach ($this->routes as $path => $routeData) {
            if ($requestPath     == $path) {
                if ($method == $routeData['method']) {
                    return $routeData['callback'];
                }
            }
        }
    }
}