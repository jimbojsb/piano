<?php
namespace Piano\Traits;

trait Configure
{
    protected $config;
    protected $environment;

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        define("PIANO_ENV", $environment);
        return $this;
    }

    public function configureFrom($configFile)
    {
        $configOptions = include APP_PATH . DIRECTORY_SEPARATOR . $configFile;
        if (is_array($configOptions)) {
            if (isset($this->environment) && isset($configOptions[$this->environment])) {
                $envConfigOptions = $configOptions[$this->environment];
                if ($envConfigOptions["extends"]) {
                    $configOptions = array_merge_recursive($configOptions[$envConfigOptions["extends"]], $envConfigOptions);
                } else {
                    $configOptions = $envConfigOptions;
                }
            }
            $this->config = new \Piano\Config($configOptions);
        }
    }

}