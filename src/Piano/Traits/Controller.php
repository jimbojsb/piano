<?php
namespace Piano\Traits;

trait Controller
{
    protected $config;
    protected $resourceManager;
    protected $request;
    protected $response;
    protected $application;

    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setResourceManager($resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }

    public function getResourceManager()
    {
        return $this->resourceManager;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}