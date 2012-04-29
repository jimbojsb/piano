<?php
spl_autoload_register(function($className) {
    if (strpos($className, "Piano\\") === 0) {
        $fileName = str_replace("\\", DIRECTORY_SEPARATOR, $className) . ".php";
        require_once __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    }
});