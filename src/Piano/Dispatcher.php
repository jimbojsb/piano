<?php
namespace Piano;

class Dispatcher
{
    public static function dispatch(Route $route)
    {

    }

    private static function loadController($controllerName)
    {

    }

    public function loadControllers($controllers)
        {
            if (is_string($controllers)) {
                $di = new \DirectoryIterator($controllers);
                foreach ($di as $item) {
                    if ($item->isFile()) {
                        $controllerName = str_replace('.php', '', strtolower($item->getFileName()));
                        $this->controllers[$controllerName] = $item->getPathName();
                    }
                }
            }
            return $this;
        }
}