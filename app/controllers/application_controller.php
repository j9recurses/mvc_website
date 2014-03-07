<?php

class ApplicationController extends Base {
    
    public $layout = '';
    public $out = '';
    public $except = array('admin','upload');
    
    function __construct(Request $request) {

        $this->r = $request;

        // check to see if the user needs to be registered to
        // view the requested page.
        if(isset($this->except) && in_array($this->r->action,$this->except)) {
            if(!$this->logged_in()) {
                $_SESSION['message'] = 'You must be logged in to visit the page you have requested.';
                header('Location:?_r=default/please_login');
                exit;
            }
        }
    }

    function message() {
        if(isset($_SESSION['message'])) {
            $msg =    $_SESSION['message'];
            unset($_SESSION['message']);
            return $msg;
        }
    }

    function logged_in() {
        if(isset($_COOKIE['logged_in'])) {
            return true;
        }
        return false;
    }
    
    function render($as = 'html') {
        // Perfect candidate for the Decorator class
        // as=doit  $this->r->as 
        $this->decorator = strtolower($as);
        // changes fro html to doit
        if(isset($this->r->as)) $this->decorator = $this->r->as;
        $this->controller = $this->r->controller;
        $this->action = $this->r->action;
        $this->id = $this->r->id;
        $this->mystuff2 = $this->r->mystuff2;
        $this->mystuff3 = $this->r->mystuff3;

        if(file_exists(DECORATORS . $this->decorator .'_decorator.php')) {
            // htmlDecorator is the name of the HTML decorator which will render the HTML
            // html
            // htmlDecorator
            $decorator = $this->decorator . 'Decorator';
            // autoload will go to app/controllser/decorators/html_decorator.php
            $decorator = new $decorator;
            $decorator->render($this);
            exit;
        }
        else {
            $decorator = new htmlDecorator;
            $decorator->render($this);
        }
        exit;
    }

    function yield() {
        print $this->out;
    }

    function forNavigation()
    {
       $dh = opendir(CONTROLLERS);
       $out = array();
       while($file = readdir($dh)) {
          if($file == '.') continue;
          if($file == '..') continue;
          if($file == 'application_controller.php') continue;
          if(strpos($file,'.') == 0) continue;
          if(!strstr($file,'.php')) continue;
          $klass = substr($file,0,strpos($file,'_controller'));
          $out[] = $klass;
       } 
       return $out;
    }
}


