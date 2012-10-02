<?php
namespace Piano\Route;

use \Piano\Request;

interface RouteInterface
{
    public function match(Request $request);
    public function getCallback();
    public function getParams();
}