<?php
    class homeController extends Controller {
        private $user;
        private $dbHandle;

        function __construct()
        {
            session_start();
            $this-> user= $this->model('user');//callback the model method;
            $this-> dbHandle= $this-> user->db();//create a handle for the database;
        }


     //function common which contains code that is common to the various class methods
        function _common() {
            /*
             *check if the access code is stored in the user's session
             * This code is used to check if a user is registered or logged in
             * Unregistered users are redirected to the login page and are unable to access the site
             */
            if(!isset($_SESSION['access_code'])){
                header('location: /auth/login');
            }

            //Throw and catch an error generated if the site is accessed but user does not exist in the database
            try{
                $result= $this-> dbHandle-> selectFromUsersTable('i', $_SESSION['access_code']);
                if(empty($result)){
                    throw new Exception();
                }
                $row= $this-> dbHandle-> getRows($result);
                return $row;
            }catch(Exception $e){
                die('A Problem Occurred: please return to homepage');
            }

        }

        function index() {
            session_destroy();
            $this->view('home/homepage', []);
        }

        function confirm() {
            $row= $this-> _common();
            $firstName= $row[0]['FIRST NAME'];
            $lastName= $row[0]['LAST NAME'];
            $accessCode= $row[0]['ACCESS CODE'];

            //render the view
            $this->view('home/confirm', ['firstName'=> $firstName, 'lastName'=> $lastName, 'accessCode'=> $accessCode]);
        }

        function status() {

            $row= $this-> _common();
            $firstName= $row[0]['FIRST NAME'];
            $lastName= $row[0]['LAST NAME'];
            $accessCode= $row[0]['ACCESS CODE'];
            $address= $row[0]['ADDRESS'];
            $dateOfBirth= $row[0]['DATE OF BIRTH'];
            $subjects= explode(',', $row[0]['SUBJECTS']);
            $image= $row[0]['IMAGE'];

            //render the view
            $this->view('home/status', ['firstName'=> $firstName, 'lastName'=> $lastName, 'accessCode'=> $accessCode,
                'address'=> $address, 'subjects'=> $subjects, 'dateOfBirth'=> $dateOfBirth, 'image'=> $image
            ]);
        }

        function detail() {

            $row= $this-> _common();
            $firstName= $row[0]['FIRST NAME'];
            $lastName= $row[0]['LAST NAME'];
            $accessCode= $row[0]['ACCESS CODE'];
            $address= $row[0]['ADDRESS'];
            $dateOfBirth= $row[0]['DATE OF BIRTH'];
            $subjects= explode(',', $row[0]['SUBJECTS']);
            $image= $row[0]['IMAGE'];
            $religion= $row[0]['RELIGION'];
            $state= $row[0]['STATE'];
            $maritalStatus= $row[0]['MARITAL STATUS'];
            $education= $row[0]['EDUCATION'];
            //render the view
            $this->view('home/detail', ['firstName'=> $firstName, 'lastName'=> $lastName, 'accessCode'=> $accessCode,
                'image'=> $image,
                'tableRows'=> ['Address'=>$address, 'Marital Status'=>$maritalStatus, 'Educational Background'=>$education, 'Select Best Subject'=>$subjects,
                    'Religion'=>$religion, 'State of Origin'=>$state, 'Date of Birth'=>$dateOfBirth
                ]
            ]);
        }

    }