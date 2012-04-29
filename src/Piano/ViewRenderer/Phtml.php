<?php
namespace Piano\ViewRenderer;

class Phtml
{
    public function render($viewScript, array $data = null, $isolate = false)
    {
        if ($data) {
            extract($data, EXTR_SKIP);
        }

        ob_start();
        include implode(
            DIRECTORY_SEPARATOR,
            [
                APP_PATH,
                'views',
                $viewScript
            ]
        );
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
}