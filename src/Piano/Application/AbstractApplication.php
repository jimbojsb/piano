<?php
namespace Piano\Application;

abstract class AbstractApplication
{
    private $resourceManager;

    public function __construct($path)
    {
        define('APP_PATH', realpath($path));
        $this->resourceManager = new \Piano\ResourceManager();
    }

    public function getResource($resource)
    {
        return $this->resourceManager->getResource($resource);
    }

    public function addResource($name, $resource)
    {
        $this->resourceManager->addResource($name, $resource);
    }

    abstract public function run();
}