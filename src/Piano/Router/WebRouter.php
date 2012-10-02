<?php
namespace Piano\Router;

use \Piano\Route\WebRoute;

class WebRouter extends AbstractRouter
{
    protected function newRoute()
    {
        return new WebRoute();
    }
}