<?php
    class HTMLDecorator extends Decorator{

        function render(ApplicationController $app) 
        {
            $controller = $app->r->controller;
            $action     = $app->r->action;
            $id         = $app->r->id;
            $this->app  = $app;

            if(file_exists(VIEWS . $controller . '/' . $action . '.html')) {
                if(method_exists($app,$action)) {
                   $app->$action();
                }
                ob_start();
                include VIEWS . $controller . '/' . $action . '.html';
                $app->out = ob_get_clean();
                if(file_exists(LAYOUTS . $controller.'.html')) {
                    include LAYOUTS . $controller . '.html';
                }
                elseif(file_exists(LAYOUTS .'default.html')) {
                    include LAYOUTS . 'default.html';
                }
                else {
                    print $app->out;
                }
            }
            else {
                $app->r->controller = 'default';
                $app->r->action = 'error404';
                $obj = new DefaultController($app->r);
                $obj->render();
            }
        }
    }
?>
