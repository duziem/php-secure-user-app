<?php
    class authController extends Controller {
        function __construct()
        {
            session_start();
            ob_start();
        }

        //Log in a user after validating the code entered into the input field
        function login() {
            session_unset();
            $codeMatchError= '';//this var will contain the error message when the code assigned by admin and code submitted by an applicant does not match
            $requiredError= ''; //this var will contain the error message when an applicant attempts to submit an empty field


        /**
         * if the form is submitted this code is executed
         */
            if(isset($_POST['activateBtn'])){
                if(isset($_POST['activationCode'])) {
                    if (!empty($_POST['activationCode'])) {
                        $activationCode= $_POST['activationCode'];//activation code from log in form


            /**
             * This is the activation code that would be assigned to the applicant by an administrator
             *   This code normally would be sent to an applicant via a medium such as the user's Email
             *   for the sake of this application the code has been predefined
             */
                       $activationCodeFromAdmin= 10859;

                        //compare the two activation codes
                        if($activationCode == $activationCodeFromAdmin){

                            //create a csrf token for an extra layer of protection
                            if(!isset($_SESSION['csrf_token'])) {

                                //if no csrf token present create a new one
                                $token= bin2hex(openssl_random_pseudo_bytes(24));
                                $_SESSION['csrf_token']= $token;
                            }

                            $user= $this->model('user');//callback the model method
                            $dbHandle= $user->db();//create a handle for the database
                            //create a table to store the activation code temporarily
                            $dbHandle->createTempTable();
                            $dbHandle->InsertIntoTempTable('i', [$activationCode]);
                            //$_SESSION['access_code']= $activationCode;//store the temporary user id in the user session
                            unset($_POST);
                            header('location: /auth/apply');
                            ob_end_flush();
                        } else {
                            $codeMatchError= 'Incorrect code: Check code and try again';
                        }

                    } else {
                        $requiredError = 'Activation code is required';
                    }
                }
            }

            //Render the view: call the view method and assign it the arguments: Name of the view file, Array containing the error messages
            $this->view('auth/login', ['codeMatchError'=> $codeMatchError,
            'requiredError'=> $requiredError]);
        }//End of login Method


        function apply() {

            //create an array that will contain the error messages thrown
            $_SESSION['errors']= [];

            $requiredFields= ['firstName', 'lastName', 'address', 'state', 'education', 'dateOfBirth', 'religion', 'maritalStatus'];

            if(isset($_POST['applyBtn'])) {
                foreach ($_POST as $formFieldName => $formFieldValue) {
                    if(array_key_exists($formFieldName, $requiredFields)) {
                        if(isset($_POST[$formFieldName])) {
                            try {
                                if(empty($_POST[$formFieldName])) {
                                    throw new Exception($formFieldName. 'field is required');
                                }else {

                                    if($formFieldName === 'firstname' || $formFieldName === 'lastname') {
                                        $pattern= "/^[A-Za-z]+$/i";
                                        try {
                                            if(!preg_match($pattern, $_POST[$formFieldName])) {
                                                throw new Exception('Invalid Value entered in'. $formFieldName. 'field: Check' . $formFieldName. 'field and try again');
                                            }
                                        }catch (Exception $e) {
                                            $_SESSION['errors'][]= $e->getMessage();
                                        }

                                    }elseif ($formFieldName === 'state' || $formFieldName === 'education') {
                                        $pattern= "/^[A-Za-z ]+$/i";
                                        try {
                                            if(!preg_match($pattern, $_POST[$formFieldName])) {
                                                throw new Exception('Invalid Value entered in'. $formFieldName. 'field: Check' . $formFieldName. 'field and try again');
                                            }
                                        }catch (Exception $e) {
                                            $_SESSION['errors'][]= $e->getMessage();
                                        }

                                    }elseif ($formFieldName === 'address') {
                                        $pattern= "/^[-A-Za-z0-9, ]+[A-Za-z]+$/i";
                                        try{
                                            if(!preg_match($pattern, $_POST[$formFieldName])) {
                                                throw new Exception('Invalid Value entered in'. $formFieldName. 'field: Check' . $formFieldName. 'field and try again');
                                            }
                                        }catch (Exception $e) {
                                            $_SESSION['errors'][]= $e->getMessage();
                                        }
                                    }
                                }
                            }catch (Exception $e) {
                                $_SESSION['errors'][]= $e->getMessage();
                            }

                        }

                    }
                }//End of foreach loop

                //protection against cross site request forgery (csrf): validate the token submitted via the apply form against the token stored in the user session
                try {
                    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                        throw new Exception('Invalid token');
                    }
                }catch (Exception $e) {
                    $_SESSION['errors'][]= $e->getMessage();
                }
                /**
                 * check if there are any errors contained in the session
                 * if no errors grant applicant access into the site
                 */
                    if(count($_SESSION['errors']) > 0) {
                        header('location: /auth/error');
                    }else{
                        $user= $this->model('user');//callback the model method
                        $dbHandle= $user->db();//create a handle for the database

                        $result= $dbHandle-> selectFromTempTable('i', 1);

                        /**
                         * get the rows of the table
                         */
                        $row= $dbHandle-> getRows($result);
                        /**
                         * get the rows of the access code column
                         */
                        $accessCode= $row[0]['ACCESS CODE'];

                        /**
                         * check to see if the applicant has already been registered
                         * to avoid applicants registering more than once
                         *
                         */
                        $response= $dbHandle-> selectFromUsersTable('i', $accessCode);
                        if($response){
                        /**
                         * if the applicant is already registered set the flag is_registered to true
                         * delete the table 'temp' created in the login method
                         * then redirect the applicant back to the landing page / home page
                         */
                            $is_registered= true;
                            $dbHandle-> deleteTempTable();
                            header('location: /');
                        }else{
                            $is_registered= false;
                            /**
                             * save access code to user's session
                             * This access code saved in the user's session is used to carry out authentication within the site
                             * when an attempt is made to access the site to prevent unauthorized users from accessing info on the site
                             */
                            $_SESSION['access_code']= $row[0]['ACCESS CODE'];
                        }
                    }

                    if($is_registered === false) {
                        function parseFormFields() {

                            function filterInput($input) {
                                $input= trim($input);
                                $input= stripslashes($input);
                                $input= htmlspecialchars($input);
                                $input= strtolower($input);
                                return $input;
                            }

                            $firstName= filterInput($_POST['firstName']); //First name field data
                            $lastName= filterInput($_POST['lastName']); //Last Name field data
                            $address= filterInput($_POST['address']); //address field data
                            $maritalStatus= $_POST['maritalStatus']; // Marital Status field data
                            $education= filterInput($_POST['education']); //Educational background field data
                            $religion= $_POST['religion']; //Religion field data
                            $state= filterInput($_POST['state']); //State of origin field data
                            $image= $_FILES['image']['name'] ?? ''; //Image Upload field data
                            $dateOfBirth= $_POST['dateOfBirth']; //Date of Birth field data


                            // Get the data submitted through the Select Best Subject field checkboxes
                            $mathematics= $_POST['Mathematics'] ?? '';
                            $english= $_POST['English'] ?? '';
                            $science= $_POST['Science'] ?? '';
                            $government= $_POST['Government'] ?? '';
                            $art= $_POST['Art'] ?? '';
                            $civic= $_POST['Civic'] ?? '';
                            $computer= $_POST['Computer'] ?? '';
                            $history= $_POST['History'] ?? '';
                            $agriculture= $_POST['Agriculture'] ?? '';
                            $subjects= [$mathematics, $english, $science, $government, $art, $civic, $computer, $history, $agriculture];
                            $selectedSubjects= [];
                            for ($i= 0;$i < count($subjects);$i++){
                                if($subjects[$i] === ""){
                                    continue;
                                }else{
                                    $selectedSubjects[]= $subjects[$i];
                                }
                            }
                            //set the data collected from the checkbox as a sequence of strings
                            $subjects= implode(',', $selectedSubjects);

                            //store all the collected form data in an array
                            return $formData= ['firstName'=>$firstName, 'lastName'=>$lastName, 'address'=>$address, 'maritalStatus'=>$maritalStatus,
                                'subjects'=>$subjects, 'religion'=>$religion,  'state'=>$state, 'image'=>$image, 'dateOfBirth'=>$dateOfBirth, 'education'=>$education
                            ];
                        }
                        $formData= parseFormFields();
                        $accessCode= $_SESSION['access_code'];//assign the access code stored in user's session to a variable

                            /**
                             * save uploaded image to images directory
                             */
                            if(file_exists('images')) {
                                move_uploaded_file($_FILES['image']['tmp_name'],'images/' . $formData['image']);
                            }

                            /**
                             * create user table and insert user data into user table
                             */
                            $dbHandle-> createUsersTable();
                            $dbHandle-> InsertIntoUsersTable('issssssssss', ['accessCode'=> $accessCode,'firstName'=>$formData['firstName'], 'lastName'=>$formData['lastName'], 'address'=>$formData['address'],
                                'maritalStatus'=>$formData['maritalStatus'], 'education'=>$formData['education'], 'subjects'=>$formData['subjects'], 'religion'=>$formData['religion'], 'state'=>$formData['state'],
                                'image'=>$formData['image'], 'dateOfBirth'=>$formData['dateOfBirth']
                            ]);

                            /**
                             * delete the table 'temp'
                             */
                            $dbHandle-> deleteTempTable();
                            unset($_SESSION['csrf_token'], $_POST);
                            header('location: /home/confirm');
                            ob_end_flush();

                    }
            }


            //Render the view: call the view method and assign it the arguments: Name of the view file, Array containing the error messages
            $this->view('auth/apply', ['fields'=> ['firstName'=>'First Name', 'lastName'=>'Last Name', 'address'=>'Address',
                'maritalStatus'=>'Marital Status', 'education'=>'Educational Background', 'subjects'=>'Select Best Subject',
                'religion'=>'Religion', 'state'=>'State of Origin', 'dateOfBirth'=>'Date of Birth', 'image'=>'Image Upload']]);

        }//End of apply Method

        function error(){
            $this->view('auth/error', ['errors'=> $_SESSION['errors']]);

        }//End of Error Method


        function recover() {
            session_unset();
            $codeMatchError= '';
            $requiredError= ''; //this var will contain the error message when user attempts to submit an empty mail



            //if the activateBtn is set this code is executed
            if(isset($_POST['activateBtn'])){
                if(isset($_POST['activationCode'])) {
                    if (!empty($_POST['activationCode'])) {
                        $activationCode= $_POST['activationCode'];//activation code submitted from recover form

                        $user= $this->model('user');//callback the model method
                        $dbHandle= $user->db();//create a handle for the database
                        $result= $dbHandle-> selectFromUsersTable('i', $activationCode);
                        //$dbHandle-> numRows($result);
                        if($dbHandle-> numRows($result)){
                            $_SESSION['access_code']= $activationCode;
                            unset($_POST);
                            header('location: /home/confirm');
                            ob_end_flush();
                        } else {
                            $codeMatchError= 'Incorrect code: Check code and try again';
                        }

                    } else {
                        $requiredError = 'Activation code is required';
                    }
                }
            }
            //Render the view: call the view method and assign it the arguments: Name of the view file, Array containing the error messages
            $this->view('auth/recover', ['codeMatchError'=> $codeMatchError,
                'requiredError'=> $requiredError]);
        }//End of recover Method
    }