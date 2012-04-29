<?php
namespace Piano\Traits;

trait Singleton
{
    protected static $instance;

    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}