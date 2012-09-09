<?php
class Piano
{
    protected static $instance;

    protected $config;
    protected $workingDir;

    public static function play($configFilePath)
    {
        $p = self::getInstance();
        $config = require $configFilePath;
        $p->config = $config;
        $p->workingDir = realpath(dirname($configFilePath));
        $callback = $p->route($_SERVER['REQUEST_URI']);
        $p->dispatch($callback);
    }

    private function route($url)
    {
        $callback = null;
        $urlParts = @parse_url($_SERVER['REQUEST_URI']);
        foreach ($this->config["routes"] as $route => $destination) {

            if (is_array($route)) {

            } else {
                $routePath = $route;
            }

            if ($routePath == $urlParts['path']) {
                if (is_array($destination)) {
                    require_once $this->workingDir . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . key($destination);
                    $callback = $destination[key($destination)];
                } else {
                    $callback = require_once $this->workingDir . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . $destination;
                }
                break;
            }
        }
        return $callback;
    }

    private function dispatch($callback)
    {
        if (!is_callable($callback)) {

        }
        ob_flush();
        ob_start();
        $callback();
        $output = ob_get_contents();
        ob_end_clean();
        echo $output;
        exit();
    }



    protected static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }
}