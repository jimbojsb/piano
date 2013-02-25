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

        $request3 = new Request([
            'REQUEST_URI'    => '/foo',
            'REQUEST_METHOD' => 'GET'
        ]);

        $request4 = new Request([
            'REQUEST_URI'    => '/foo/',
            'REQUEST_METHOD' => 'GET'
        ]);

        $request5 = new Request([
            'REQUEST_URI'    => '/foo/bar/:baz',
            'REQUEST_METHOD' => 'GET'
        ]);

        $route = new Route;
        $route('GET /', 'foo');

        $this->assertEquals('foo', $route->getCallback(), 'route failed to return specified callback');
        $this->assertTrue($route->match($request1), 'simple path matching failed');
        $this->assertFalse($route->match($request2), 'route matched an unspecified reqeuest method');

        $route = new Route;
        $route('GET,POST /', 'foo');

        $this->assertTrue($route->match($request1), 'route failed to matched specified request method(s)');
        $this->assertTrue($route->match($request2), 'route failed to matched specified request method(s)');


        $route = new Route;
        $route('GET /foo', 'bar');

        $this->assertTrue($route->match($request3), 'route did not match without a trailing /');
        $this->assertTrue($route->match($request4), 'route did not match with a trailing /');

        $route = new Route;
        $route('GET /foo/:bar');

        $this->assertFalse($route->match($request3), 'route matched too specific of a route definition');
        $this->assertFalse($route->match($request5), 'route matched too specific a request path');


    }

    public function testRouteParameterExtraction()
    {
        $request1 = new Request([
            'REQUEST_URI'    => '/foo/bar/baz',
            'REQUEST_METHOD' => 'GET'
        ]);

        $route = new Route;
        $route('GET /:param1/:param2/:param3');

        $this->assertTrue($route->match($request1));
        $expectedParams = [
            'param1' => 'foo',
            'param2' => 'bar',
            'param3' => 'baz'
        ];

        $this->assertEquals($expectedParams, $route->getParams(), 'route failed to return expected parameters');

    }
}