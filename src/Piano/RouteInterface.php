<?php
namespace Piano;

interface RouteInterface
{
    public function match(Request $request);
}