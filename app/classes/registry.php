<?php
   class Registry extends Base {
      static private $data = array();

      private function __construct() {}

      static public function get($key) 
      {
         return self::$data[$key];
      }   

      static public function set($key,$value) 
      {   
         self::$data[$key] = $value;
      }   
   }
