<?php
    // &as=plain_text
    class Plain_TextDecorator {
        function render(ApplicationController $app) 
        {
            $controller = $app->r->controller;
            $action     = $app->r->action;
            $id         = $app->r->id;
            // Set the MIME type (media type) to plain text
            header('Content-type: text/plain');

            if(file_exists(VIEWS . $controller . '/' . $action . '.html')) {
                if(method_exists($app,$action)) {
                   $app->$action(); // polymorphism
                }

                // ob_start() sends output to the print buffer and holds it there.
                ob_start();
                // include app/views/users/index.html
                include VIEWS . $controller . '/' . $action . '.html';
                $app->out = ob_get_clean();
                // Is there a layout for this controller
                if(file_exists(LAYOUTS . $controller.'.html')) {
                    include LAYOUTS . $controller . '.html';
                }
                elseif(file_exists(LAYOUTS .'default.html')) {
                    include LAYOUTS . 'default.html';
                }
                else {
                    print $app->out;
                }
                exit;
            }
        }
    }
?>
