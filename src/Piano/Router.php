<?php
namespace Piano;

use \Piano\Route,
    \Piano\Request;

class Router implements \ArrayAccess
{
    protected $routes = array();
    protected $notfound;
    protected $error;

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

    public function notfound($route = null)
    {
        if ($route) {
            $r = new Route();
            $r->route(null, $route);
            $this->notfound = $r;
        } else {
            return $this->notfound;
        }

    }

    public function error($route)
    {
        if ($route) {
            $r = new Route();
            $r->route(null, $route);
            $this->error = $r;
        } else {
            return $this->error;
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
            $this->routes[$offset] = new Route();
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