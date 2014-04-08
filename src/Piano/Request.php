<?php
namespace Piano;

class Request
{
    protected $scheme;
    protected $host;
    protected $path;
    protected $query;
    protected $method;
    protected $params = array();
    protected $headers = [];

    public function __construct($data)
    {
        $this->scheme = ($data['HTTPS']) ? 'https' : 'http';
        $this->method = $data['REQUEST_METHOD'];
        $this->host = $data['HTTP_HOST'];

        $urlParts = @parse_url($data['REQUEST_URI']);
        $this->path = $urlParts['path'];
        $this->query = $urlParts['query'];
        if (function_exists('\getallheaders')) {
            $this->headers = \getallheaders();
        }

    }

    public function __call($method, $params)
    {
        switch (strtolower($method)) {
            case "ispost":
            case "isget":
            case "isput":
            case "isdelete":
                $actualMethod = strtoupper($this->method);
                $requestedMethod = strtoupper(str_replace('is', '', $method));
                return $actualMethod === $requestedMethod;
        }
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

    public function isSecure()
    {
        return $this->scheme === 'https';
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($headerName)
    {
        return $this->headers[$headerName];
    }
}