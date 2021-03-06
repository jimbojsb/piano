<?php
namespace Piano;

use \Piano\Request;

class Route implements RouteInterface
{
    protected $method;
    protected $path;
    protected $callback;
    protected $params = array();

    public function getCallback()
    {
        return $this->callback;
    }

    public function getParams()
    {
        return $this->params;
    }


    public function __invoke($route, $callback, $params = array())
    {
        if ($route) {
            list($method, $path) = explode(' ', $route);
            if (strpos($method, ',') !== false) {
                $method = explode(',', $method);
            }
            $this->path = $path;
            $this->method = $method;
        } else {
            $this->method = null;
            $this->path = null;
        }
        $this->callback = $callback;
        $this->params = $params;
    }

    public function match(Request $request)
    {
        $pathMatches = false;
        $paramSearchRegex = '`:[a-z0-9]+`';
        $paramReplacementRegex = '([^/]+?)';
        $paramNames = array();
        $pathRegex = preg_replace_callback($paramSearchRegex, function($matches) use (&$paramNames, $paramReplacementRegex) {
            $paramNames[] = str_replace(':', '', $matches[0]);
            return $paramReplacementRegex;
        }, $this->path);
        $requestPath = $request->getPath();
        if (substr_count($request->getPath(), '/') > 1 && substr($requestPath, strlen($requestPath) - 1) == '/') {
            $matchablePath = substr($requestPath, 0, strlen($requestPath) - 1);
        } else {
            $matchablePath = $request->getPath();
        }

        if (preg_match("`^$pathRegex$`", $matchablePath, $matches)) {
            if (is_string($this->method) && ($this->method === $request->getMethod())) {
                $pathMatches = true;
            } else if ($this->method === null) {
                $pathMatches = true;
            } else if (is_array($this->method) && in_array($request->getMethod(), $this->method)) {
                $pathMatches = true;
            }
        }

        if ($pathMatches) {
            $paramValues = array_slice($matches, 1);
            array_walk($paramValues, function(&$val) {
                $val = urldecode($val);
            });
            $params = array_combine($paramNames, $paramValues);
            $this->params = array_merge($this->params, $params);
            return true;
        }
        return false;
    }
}