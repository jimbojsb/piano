<?php
namespace Piano;

class ClassLoader
{
    protected $namespaces = array();

    public function __construct()
    {
        spl_autoload_register(array($this, 'load'));
    }
    public function registerNamespace($namespacePrefix, $filesystemPrefix)
    {
        $this->namespaces[$namespacePrefix] = $filesystemPrefix;
    }

    public function load($className)
    {
        $className = '\\' . $className;
        foreach ($this->namespaces as $namespace => $prefix) {
            if ($namespace == substr($className, 0, strlen($namespace))) {
                $fileName = str_replace($namespace, '', $className);
                $fileName = $prefix . str_replace("\\", '/', $fileName) . ".php";
                require_once $fileName;
            }
        }
    }
}