<?php
namespace Piano\Application;

abstract class AbstractApplication
{
    private $resourceManager;

    public function __construct()
    {
        $this->resourceManager = new \Piano\ResourceManager();
    }

    protected  function getResource($resource)
    {
        return $this->resourceManager->getResource($resource);
    }

    protected function addResource($name, $resource)
    {
        $this->resourceManager->addResource($name, $resource);
    }

    public function __get($property)
    {
        return $this->getResource($property);
    }

    public function __set($property, $value)
    {
        $this->addResource($property, $value);
    }

    abstract public function run();
}