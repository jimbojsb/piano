<?php
namespace Piano\Traits;

trait Route
{
    protected $routes;

    /**
     * @param $route
     * @return Route;
     */
    public function route($route)
    {
        $route = new \Piano\Route($route);
        $this->routes[] = $route;
        return $route;
    }

    public function dispatch(\Piano\Request $request)
    {
        foreach ($this->routes as $route) {
            if ($route->match($request)) {

            }
        }
    }

}