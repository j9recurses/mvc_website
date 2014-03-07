<?php
    class DoItDecorator extends Decorator{

        // request // userController// applicationController -> render // Decorator->render()
        /*
         * The DoItDecorator runs an a method and exits;
         */
        function render(ApplicationController $app) 
        {
            $controller = $app->r->controller;
            $action     = $app->r->action;
            $id         = $app->r->id;

            //_r=user/checkuser&as=doit
            // controller = user
            // action = checkuser
            // doitDecorator 
            if(file_exists(CONTROLLERS . $controller . '_controller.php')) {
                require_once CONTROLLERS . strtolower($controller) .'_controller.php';
                $klass = $controller.'Controller';
                $app = new $klass($app->r);

                if(method_exists($app,$action)) {
                   $app->$action();
                   exit;
                }
            }
        }
    }
?>
