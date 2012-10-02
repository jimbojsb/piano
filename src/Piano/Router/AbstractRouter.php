<?php
namespace Piano\Router;

use \Piano\Route\RouteInterface,
    \Piano\Request;

abstract class AbstractRouter implements \ArrayAccess
{
    private $routes = array();

    abstract protected function newRoute();

    public function route(Request $request)
    {
        foreach ($this->routes as $name => $route) {
            if ($route->match($request)) {
                return array(
                    'callback' => $route->getCallback(),
                    'params'   => $route->getParams()
                );
            }
        }
    }

    protected function addRoute(RouteInterface $route)
    {
        $this->routes[] = $route;
    }

    public function offsetExists($offset)
    {
        return $this->routes[$offset];
    }

    public function offsetGet($offset)
    {
        if (!isset($this->routes[$offset])) {
            $this->routes[$offset] = $this->newRoute();
        }
        return $this->routes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->routes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->routes[$offset]);
    }
}