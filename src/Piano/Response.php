<?php
namespace Piano;

class Response
{
    protected $statusCode = 200;
    protected $headers;
    protected $body ;

    public function __construct($body = '')
    {
        if (is_object($body)) {
            $this->body = $body->__toString();
        } else {
            $this->body = "$body";
        }
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

    public function __toString()
    {
        http_response_code($this->statusCode);
        return $this->body;
    }
}
