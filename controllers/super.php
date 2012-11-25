<?php

/*
 * Controller.php
 */

class Controller {
    protected $model;
    protected $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view = $view;
    }
}

class Auth extends Controller {
    public $user;
    function __construct($model, $view) {
        parent::__construct($model, $view);
    }
}
