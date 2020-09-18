<?php
    class Controller{
        //protected $view;
        //protected $model;

        function view($viewFile, $viewData) {
            new View($viewFile, $viewData);
        }
        function model($modelName) {
            require_once Model . $modelName. '.php';
            return new $modelName;
        }
    }