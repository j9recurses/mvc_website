<?php

class TextileDecorator extends Decorator {
    function render(ApplicationController $app) {
        $this->app = $app;
        $this->app->wumpus = 'Bumpus';
        $textile = new Textile;
        if (file_exists(VIEWS.$this->app->r->controller.'/'.$this->app->r->action.'.html')) {
            if (method_exists($this->app->r->controller.'Controller', $this->app->r->action)) {
                $action = $this->app->r->action;
                $this->app->$action();
            }
            ob_start();
              include (VIEWS.$this->app->r->controller.'/'.$this->app->r->action.'.html');
              $this->app->out = $textile->textileThis(ob_get_clean());
            } else {
            // handle error
                $obj = new DefaultController;
                $obj->action = 'error404';
                $obj->render();
          }
          if (file_exists(LAYOUTS.$this->app->r->controller.'.html')) {
              include LAYOUTS.$this->app->r->controller.'.html';
          } elseif (file_exists(LAYOUTS.'default.html')) {
              include LAYOUTS.'default.html';
          } else {
              print $textile->TextileThis($this->app->out);
          }
     } 

     function yield() {
        print $this->app->out;
     }
}


