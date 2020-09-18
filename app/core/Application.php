<?php
    class Application{
        //private $controller= 'authController';
        private $controller= 'homeController';
        //private $action= 'login';
        private $action= 'index';
        private $params= [];

        function __construct() {
            $this->parseUrl();


            if (file_exists(Controllers  . $this->controller . '.php')) {
               // echo $this->controller;
               require_once Controllers . $this->controller . '.php';
                $this->controller= new $this->controller;
                if (method_exists($this->controller, $this->action)) {
                    //echo 'Data exists';
                    call_user_func_array([$this->controller, $this->action], $this->params);
                }
            }


        }


        public function parseUrl() {
            //Extract and trim the requested url query string
            $request= trim($_SERVER['REQUEST_URI'], '/');

            if(!empty($request)) {
                //convert the extracted url query string into an array
                $url= explode('/', filter_var($request, FILTER_SANITIZE_URL));


               //assign the first value of the url array to the controller variable
               $this->controller= isset($url[0]) ? $url[0] .'Controller' : 'homeController';
               //assign the second value of the url array to the action variable
               $this->action= isset($url[1]) ? $url[1] : 'index';

               unset($url[0], $url[1]);
               //assign the remaining members of the array to the params variable
               $this->params= !empty($url) ? array_values($url) : [];

            }
        }
    }