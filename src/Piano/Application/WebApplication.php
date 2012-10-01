<?php
namespace Piano\Application;

use \Piano\Dispatcher,
    \Piano\Router\WebRouter;

class WebApplication extends AbstractApplication
{
    public function __construct($path)
    {
        parent::__construct($path);
        $this->addResource('dispatcher', new Dispatcher());
        $this->addResource('router', new WebRouter());
    }

    public function run()
    {
        $request = new Request($_SERVER);
        $response = $this->getResource('dispatcher')->dispatch($request);
        echo $response;
    }
}