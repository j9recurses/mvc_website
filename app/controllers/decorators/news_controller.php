<?php

class NewsController extends ApplicationController {

    function __construct($request) {
        parent::__construct($request);
    }
    function index() {
        $this->for_view = 'Hello from the ' . __CLASS__ . ':' . __METHOD__;
    }

    function entertainment() {
        print 'in' .  __CLASS__ . '::' . __METHOD__ ;
        $this->for_view = 'Hello from the ' . __CLASS__ . ':' . __METHOD__;
    }

    function fashion() {
        print 'in' .  __CLASS__ . '::' . __METHOD__ ;
        $this->for_view = 'Hello from the ' . __CLASS__ . ':' . __METHOD__;
    }

    function sports() {
        print 'in' .  __CLASS__ . '::' . __METHOD__ ;
        $this->for_view = 'Hello from the ' . __CLASS__ . ':' . __METHOD__;
    }
}
