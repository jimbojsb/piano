<?php
namespace Piano;

class Request
{
    protected $scheme;
    protected $host;
    protected $path;
    protected $query;
    protected $method;
    protected $controller;
    protected $action;
    protected $params = array();

    public function __construct($data)
    {
        $this->scheme = ($data['HTTPS']) ? 'https' : 'http';
        $this->method = $data['REQUEST_METHOD'];
        $this->host = $data['HTTP_HOST'];

        $urlParts = @parse_url($data['REQUEST_URI']);
        $this->path = $urlParts['path'];
        $this->query = $urlParts['query'];
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }
}