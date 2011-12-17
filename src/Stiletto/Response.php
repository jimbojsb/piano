<?php
namespace Stiletto;
class Response
{
    const CONTENT_TYPE_JSON = "app/json";

    protected $contentType;
    protected $body = "";

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function __toString()
    {
        $output .= $this->body;
        return $output;
    }

}