<?php
namespace Stiletto;
class App
{
    protected $env;
    protected $config;
    protected $cwd;

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

        $configFilePath = APP_PATH . '/config.php';
        if (file_exists($configFilePath)) {
            $this->config = include $configFilePath;
        } else {
            throw new \Exception($configFilePath . " not found");
        }

        require_once __DIR__ . '/Autoloader.php';
        Autoloader::register();
    }

    public function run()
    {
        $router = new Router(APP_PATH . '/routes.php', $this);
        $callback = $router->route();

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