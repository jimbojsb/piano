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

    public function __toString()
    {
        return $this->body;
    }
}
