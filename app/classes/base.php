<?php
/**
 * The Base class provides some low level methods such as __get and __set
 * so that we can don't have to go through this hassle (did I show some
 * annoyance?) of creating these methods for other classes in our site.
 *
 * $Id: base.php,v 1.1.1.1 2008/03/11 20:52:42 somedude Exp $
 */

class Base {

    /**
     * We need a private $properties array to work with __get()
     * and __set(). If we set the variables directly in a class,
     * __get() and __set() do not need to be called. In other cases,
     * Where we would have $this->var_name, we instead have
     * $this->properties['var_name'].
     *
     * @properties array
     */
    /*
    protected $properties = array();
     */
    
    /**
     * Retrieve instance variables automatically. The variables
     * are kept in the $properties array.
     *
     * @param string $name
     * @return mixed
     */
    function __get($name) {
        if(isset($this->$name)) return $this->$name;
        return null;
        /*
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        } else {
            return null;
        }
         */
    }
    
    /**
     * Set instance variables
     *
     * @param string $name
     * @param mixed $value
     */
    function __set($name, $value) {
        $this->$name = $value;
        //$this->properties[$name] = $value;
    }
}
