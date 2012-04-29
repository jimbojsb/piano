<?php
namespace Piano;

class Router
{
    use \Piano\Traits\Singleton;

    protected $routes = [];

    private function addRoute()
    {

    }

    /**
     * @param $route
     * @return Route;
     */
    public static function route($route)
    {
        $r = self::getInstance();
        $r->addRoute[] = $route;
        return $route;
    }
}