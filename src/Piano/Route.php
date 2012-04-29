<?php
namespace Piano;

class Route
{
    protected $path;
    protected $method;
    protected $callback;
    protected $acl;

    public function __construct($route)
    {
        if (strpos($route, ' ') !== false) {
            list($method, $path) = explode(' ', $route);
        } else {
            $method = null;
            $path = $route;
        }
        $this->method = $method;
        $this->path = $path;
    }

    public function to($callback)
    {
        $this->callback = $callback;
        return $this;
    }


    public function allow($role)
    {

    }

    public function deny($role)
    {

    }

    public function match(Request $request)
    {
        $pathMatches = false;
        if ($request->getPath() === $this->path) {
            if (isset($this->method) && ($this->method === $_SERVER['REQUEST_METHOD'])) {
                $pathMatches = true;
            } else if (!isset($this->method)) {
                $pathMatches = true;
            }
        }

        if ($pathMatches) {
            return true;
        }
    }
}