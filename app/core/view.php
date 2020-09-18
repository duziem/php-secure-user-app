<?php
    class View{
        public $viewData;

        function __construct($viewFile, $viewData) {
            $this->viewData= $viewData;
            $this->render($viewFile);
        }
        private function render($viewFile) {
            include Views . $viewFile . '.phtml';
        }
    }