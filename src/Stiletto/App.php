<?php
namespace Stiletto;
class App
{
    protected $env;
    protected $config;
    protected $cwd;
    protected $router;

    public function __construct()
    {
        $env = getenv('STILETTO_ENV');
        if ($env) {
            $this->env = $env;
        } else {
            throw new \Exception('STILETTO_ENV not defined');
        }

        $cwd = getcwd();
        define('APP_PATH', realpath("$cwd/../app"));

        require_once __DIR__ . '/Autoloader.php';
        Autoloader::register();

        $this->router = new Router();

        $configFilePath = APP_PATH . '/config/config.php';
        if (file_exists($configFilePath)) {
            $this->config = include $configFilePath;
        } else {
            throw new \Exception($configFilePath . " not found");
        }




    }

    public function route($route, $callback)
    {
        $this->router->addRoute($route, $callback);
    }

    public function run()
    {
        $callback = $this->router->route();

        $request = new Request();

        $rf = new \ReflectionFunction($callback);
        $params = $rf->getParameters();

        $callbackParams = array();
        foreach ($params as $param) {
            if ($param->name == 'app') {
                $callbackParams[] = $this;
            } else {
                $callbackParams[] = $request->getParam($param->name);
            }
        }

        $output = call_user_func_array($callback, $callbackParams);

        $response = new Response();
        $response->setBody($output);
        echo $response;
    }

    public function getCwd()
    {
        return $this->cwd;
    }
}