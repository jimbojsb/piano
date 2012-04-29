<?php
namespace Piano;

class Response
{

    protected $boundVars = [];
    protected $viewScript;

    protected $body;
    protected $headers = array();

    public function __construct()
    {

    }

    public function bind($key, $val)
    {
        $this->boundVars[$key] = $val;
    }

    public static function create(array $vars = [])
    {
        $r = new self();
        foreach ($vars as $key => $val) {
            $r->bind($key, $val);
        }
        return $r;
    }

    public function renderWith($viewScript)
    {
        $this->viewScript = $viewScript;
        return $this;
    }

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
        return "RESPONSE";
    }
}