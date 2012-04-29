<?php
namespace Piano;

class Application
{
    protected $dispatcher;
    protected $request;
    protected $response;
    protected $environment;
    protected $config;

    public function __construct()
    {
        define('APP_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));
        set_include_path(
            implode(
                PATH_SEPARATOR,
                [get_include_path(), realpath(APP_PATH)]
            )
        );
        $this->request = new Request($_SERVER);
        $this->response = new Response();
        $this->dispatcher = new Dispatcher();
    }

    public function loadConfigFile($configFile)
    {
        $this->config = Config::fromFile($configFile, $this->environment);
    }

    /**
     * @param $route
     * @return Route
     */
    public function route($route)
    {
        $r = new Route($route);
        $this->dispatcher->addRoute($r);
        return $r;

    }

    public function run()
    {
        $this->dispatcher->setConfig($this->config);
        while (!$this->request->hasBeenDispatched()) {
            $response = $this->dispatcher->dispatch($this->request);
        }
        echo $response;
    }


    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }
}