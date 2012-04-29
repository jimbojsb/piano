<?php
namespace Piano;

class Application
{
    protected $router;
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
    }

    public function loadConfigFile($configFile)
    {
        $this->config = Config::fromFile($configFile);
    }

    public function run()
    {

    }


    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }
}