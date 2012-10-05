<?php
namespace Piano\Application;

use \Piano\Dispatcher,
    \Piano\Request,
    \Piano\Router\WebRouter,
    \Piano\ClassLoader;

class WebApplication extends AbstractApplication
{
    public function __construct()
    {
        parent::__construct();
        $this->addResource('router', new WebRouter());
        $this->addResource('dispatcher', new Dispatcher($this->router));
        $this->addResource('classloader', new ClassLoader());

    }

    public function run()
    {
        $request = new Request($_SERVER);
        $response = $this->getResource('dispatcher')->dispatch($request);
        echo $response;
    }
}