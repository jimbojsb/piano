<?php
namespace Piano\Dispatcher;

use \Piano\Request;

class WebRoute extends AbstractRoute
{
    protected $method;
    protected $path;

    public function __construct($route, $params)
    {
        if (strpos($route, ' ') !== false) {
            list($method, $path) = explode(' ', $route);
        } else {
            $method = null;
            $path = $route;
        }
        $this->method = $method;
        $this->path = $path;
        $this->params = $params;
    }

    public function match(Request $request)
    {
        $pathMatches = false;
        if ($request->getPath() === $this->path) {
            if (isset($this->method) && ($this->method === $_SERVER['REQUEST_METHOD'])) {
                $pathMatches = true;
            } else if ($this->method === null) {
                $pathMatches = true;
            }
        }

        if ($pathMatches) {
            return true;
        }
    }
}