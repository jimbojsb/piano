<?php
namespace Piano\Dispatcher;

use \Piano\Request;

abstract class AbstractRoute
{
    protected $params;

    abstract public function match(Request $request);

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParam($paramName)
    {
        return $this->params[$paramName];
    }
}