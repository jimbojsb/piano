<?php
namespace Stiletto;
class Autoloader
{
    protected static $basePath;

    public static function register()
    {
        self::$basePath = __DIR__;
        spl_autoload_register(array(self, 'loadClass'));
    }

    public static function loadClass($classname)
    {
        if (stripos($classname, "Stiletto") !== false) {
            $classPath = str_replace('Stiletto\\', '/', $classname);
            require_once self::$basePath . $classPath . ".php";
        }
    }
}