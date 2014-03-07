<?php
class Decorator {

    function __get($name) {
        if(isset($this->app->$name)) {
            return $this->app->$name;
        }
        return false;
    }

    function __call($name, $params='') {
        if(method_exists($this->app,$name)) {
            return $this->app->$name($params);
        }
        return false;
    }
    function yield() {
        print $this->app->out;
    }
}
?>
