<?php
namespace Piano;

class Response
{
    protected $statusCode = 200;
    protected $headers = array();
    protected $body;

    public function __construct($body = '')
    {
        $this->setBody($body);
    }

    public function setBody($body)
    {
        if (is_object($body)) {
            $this->body = $body->__toString();
        } else {
            $this->body = "$body";
        }
    }

    public function asJson()
    {
        $this->addHeader('Content-type', 'application/json');
        return $this;
    }

    /**
     * @param $code int
     * @return Response
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    public function addHeader($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
    }

    public function __toString()
    {
        foreach ($this->headers as $headerName => $headerValue) {
            header("$headerName: $headerValue");
        }
        http_response_code($this->statusCode);
        return $this->body;
    }

    public function redirect($location, $statusCode = 301)
    {
        $this->addHeader('Location', $location);
        $this->setStatusCode($statusCode);
        return $this;
    }
}
