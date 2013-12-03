<?php
namespace Piano;

class Application
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct()
    {
        $this->router = new Router;
        $this->dispatcher = new Dispatcher($this->router);
    }

    public function play()
    {
        $this->dispatcher->dispatch(new Request($_SERVER));
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

}