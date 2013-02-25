<?php
use Piano\Route,
    Piano\Request;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $request1 = new Request([
            'REQUEST_URI'    => '/',
            'REQUEST_METHOD' => 'GET'
        ]);

        $request2 = new Request([
            'REQUEST_URI'    => '/',
            'REQUEST_METHOD' => 'POST'
        ]);

        $route = new Route;
        $route('GET /', 'foo');

        $this->assertTrue($route->match($request1));
        $this->assertEquals('foo', $route->getCallback());
        $this->assertFalse($route->match($request2));


    }
}