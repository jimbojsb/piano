<?php
namespace Stiletto;
class View
{
    public function __construct()
    {

    }


    public function render($viewScript)
    {
        ob_start();
        include APP_PATH . '/views/' . $viewScript;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}