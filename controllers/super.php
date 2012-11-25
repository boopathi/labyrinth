<?php

/*
 * Controller.php
 */

namespace Controller;

class Controller {
    protected $model;
    protected $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view = $view;
    }
}


