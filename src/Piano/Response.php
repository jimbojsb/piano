<?php
namespace Piano;

class Response
{
    protected $body;
    protected $headers = array();

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    public function __toString()
    {
        return $this->body ?: "";
    }
}