<?php
class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $data = [
            "REQUEST_URI" => "/foo/bar?baz=1",
            "REQUEST_METHOD" => "GET",
            "HTTP_HOST" => "test.com",
            "HTTPS" => 0
        ];
        $request = new Piano\Request($data);

        $this->assertEquals('/foo/bar', $request->getPath());
        $this->assertEquals('baz=1', $request->getQuery());
        $this->assertEquals('test.com', $request->getHost());
        $this->assertFalse($request->isSecure());
        $this->assertTrue($request->isGet());
    }
}