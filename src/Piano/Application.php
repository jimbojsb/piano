<?php
namespace Piano;

class Application
{
    protected $dispatcher;
    protected $request;
    protected $response;
    protected $environment;
    protected $config;

    public function __construct($env, $path)
    {
        define('APP_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));
        $this->request = new Request($_SERVER);
        $this->dispatcher = new \Piano\Dispatcher\Dispatcher();
        $this->config = Config::fromFile(APP_PATH . '/app/config.php', $env);
        $this->environment = $env;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function run()
    {
        $this->dispatcher->loadRoutes(APP_PATH . '/app/routes.php');
        $this->dispatcher->setConfig($this->config);
        while (!$this->request->hasBeenDispatched()) {
            $response = $this->dispatcher->dispatch($this->request);
        }
        echo $response;
    }
}