<?php
namespace Piano;

class ResourceManager
{
    private $resources;

    public function addResource($name, $resource)
    {
        $this->resources[$name] = $resource;
    }

    public function getResource($name)
    {
        $resource = $this->resources[$name];
        if ($resource instanceof \Closure) {
            return $resource();
        } else {
            return $resource;
        }
    }
}