<?php

    // This object contains methods that are used to interact with the database and perform CRUD operations
    class DbController{
        private $conn;
        private $host= 'localhost';
        private $username= 'root';
        private $password= '';

        function __construct() {
            $this->conn= $this->dbConnect();
            $this->createDb();
            $this->conn= $this->dbReConnect();
        }


        /**
         * connect to a server using servername, username, and password parameters
         */
        public function dbConnect() {
            $conn= new mysqli($this->host, $this->username, $this->password);

            //check connection
            if($conn-> connect_errno) {
                exit("Failed to connect to Database: " . $conn-> connect_error);
            }
            return $conn;
        }

        /**
         * reconnect to the server using servername, username, password and database parameters
         */
        public function dbReConnect() {
            $conn= new mysqli($this->host, $this->username, $this->password, 'users_db');

            //check connection
            if($conn-> connect_errno) {
                exit("Failed to connect to Database: " . $conn-> connect_error);
            }
            return $conn;
        }

        /**
         * Create a Database called 'users_db'
         */
        public function createDb() {
            $query= "CREATE DATABASE IF NOT EXISTS `users_db`";
            $handle= $this->conn->query($query);
            if(!$handle) {
                die("A problem occurred: go back and try again");
            }
        }

        /**
         * Get the rows of the database as an associative array
         */
        public function getRows($result) {
            if ($result->num_rows >= 1) {
                $tableRows= [];
                while ($row= $result->fetch_assoc()) {
                    $tableRows[]= $row;
                }
                return $tableRows;
            }

        }

        /**
         *  check if the user data exists in the database
         */
        public function numRows($result) {
            if ($result->num_rows >= 1) {
                return true;
            }else{return false;}
        }

        /**
         * Create a table called 'temp' that stores the Access code temporarily until the applicant is registered
         * The table contains 1 column/field name
         * The table makes use of an InnoDB engine and UTF8 character set
         * The 'ACCESS CODE' field contains the access code sent to the applicant by the admin during registration: it is required
         */
        public function createTempTable() {

            $query= "CREATE TABLE IF NOT EXISTS `users_db`.`temp` (
                `ACCESS CODE` INT(5) UNSIGNED NOT NULL
            ) ENGINE= innoDB DEFAULT CHARSET= utf8mb4";
            $handle= $this->conn->query($query);
            if(!$handle) {
                die("Failed to perform action: go back and try again");
            }

        }

        /**
         * Create a table called 'users' that stores the Applicant's info
         * The table contains 12 columns/field names
         * The table makes use of an InnoDB engine and UTF8 character set
         * The 'ID' field is dynamically set and is used as the primary key
         * The 'ACCESS CODE' field contains the access code that the applicant enters when applying: it is required
         * The 'FIRST NAME' field contains the first name of the applicant and is of datatype VARCHAR: it is required
         * The 'LAST NAME' field contains the last name of the applicant and is of datatype VARCHAR: it is required
         * The 'ADDRESS' field contains the address of the applicant and is of datatype VARCHAR: it is required
         * The 'MARITAL STATUS' field contains the marital status of the applicant and is of datatype ENUM: it is required
         *      The 'MARITAL STATUS' field stores either the value 'Single' or 'Married': it is required
         * The 'EDUCATION' field contains the Educational background of the applicant and is of datatype VARCHAR: it is required
         * The 'SUBJECTS' field contains the subjects studied by the applicant: it is required
         * The 'RELIGION' field contains the religion of the applicant and is of datatype ENUM: it is required
         *      The 'RELIGION' field stores one of the three values 'Islam', 'Christianity', 'Traditional'
         * The 'STATE' field contains the state of the applicant and is of datatype VARCHAR: it is required
         * The 'DATE OF BIRTH' field contains the date of birth of the applicant and is of datatype DATE: it is required
         * The 'IMAGE' field contains the image name of the image uploaded by the applicant and is of datatype VARCHAR
         */
        public function createUsersTable() {

            $query= "CREATE TABLE IF NOT EXISTS `users_db`.`users` (
                    `ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
                    `ACCESS CODE` INT(5) UNSIGNED NOT NULL,
                    `FIRST NAME` VARCHAR(30) NOT NULL,
                    `LAST NAME` VARCHAR(30) NOT NULL,
                    `ADDRESS` VARCHAR(255) NOT NULL,
                    `MARITAL STATUS` ENUM('Single', 'Married') NOT NULL,
                    `EDUCATION` VARCHAR(50) NOT NULL,
                    `SUBJECTS` VARCHAR(100) NOT NULL,
                    `RELIGION` ENUM('Islam', 'Christianity', 'Traditional') NOT NULL,
                    `STATE` VARCHAR(30) NOT NULL,
                    `DATE OF BIRTH` DATE NOT NULL,
                    `IMAGE` VARCHAR(255)
            ) ENGINE= innoDB DEFAULT CHARSET= utf8mb4";
            $handle= $this->conn->query($query);
            if(!$handle) {
                die("A problem occurred: go back and try again");
            }
        }

        /**
         * Select access code from the 'temp' table
         */
        public function selectFromTempTable($paramType, $paramValue) {
            $query= "SELECT `ACCESS CODE` FROM `users_db`.`temp` WHERE ?";
            $queryHandle= $this->conn->prepare($query);
            $queryHandle->bind_param($paramType, $paramValue);
            $handle= $queryHandle->execute();
            $result= $queryHandle->get_result();

            if(!$handle) {
                die("Failed to perform action: go back and try again");
            }
            return $result;
        }

    /**
     * Select user row from the 'user' table
     * The query is run using named parameters to protect against SQL injection
     */
        public function selectFromUsersTable($paramType, $paramValue) {
            try{
                $query= "SELECT * FROM `users_db`.`users` WHERE `ACCESS CODE` = ?";
                $queryHandle= $this->conn->prepare($query);
                if(empty($queryHandle)){
                    throw new Exception();
                }

                $queryHandle->bind_param($paramType, $paramValue);
                $handle= $queryHandle->execute();
                $result= $queryHandle->get_result();
                $queryHandle->close();

                if(!$handle) {
                    die("A problem occurred: go back and try again");
                }
                    return $result;
                }catch (Exception $e){
                    return 0;
                }

        }

        /**
         * Insert the access code (assigned to an applicant by the admin) into the 'temp' table
         * The query is run using named parameters to protect against SQL injection
         */
        public function InsertIntoTempTable($paramType, $paramValue) {

            $query= "INSERT INTO `users_db`.`temp`(`ACCESS CODE`) VALUES (?)";
            $queryHandle= $this->conn->prepare($query);
            $queryHandle->bind_param($paramType, $paramValue[0]);
            $handle= $queryHandle->execute();
            $queryHandle->close();
            if(!$handle) {
                die("Failed to perform action: go back and try again");
            }
        }

    /**
     * Insert a user row into the 'users' table
     * The query is run using named parameters to protect against SQL injection
     */
        public function InsertIntoUsersTable($paramType, $paramValue) {

            $query= "INSERT INTO `users_db`.`users`(`ACCESS CODE`, `FIRST NAME`, `LAST NAME`, `ADDRESS`, 
                    `MARITAL STATUS`, `EDUCATION`, `SUBJECTS`, `RELIGION`, `STATE`, `IMAGE`, `DATE OF BIRTH`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            $queryHandle= $this->conn->prepare($query);

            $queryHandle->bind_param($paramType, $paramValue['accessCode'],$paramValue['firstName'], $paramValue['lastName'], $paramValue['address'], $paramValue['maritalStatus'],
                $paramValue['education'], $paramValue['subjects'], $paramValue['religion'], $paramValue['state'], $paramValue['image'], $paramValue['dateOfBirth']
            );
            $handle= $queryHandle->execute();
            $queryHandle->close();
            if(!$handle) {
                die("A problem occurred: go back and try again");
            }
        }

        /**
         * Delete the 'temp' table from the database 'users_db'
         */
        public function deleteTempTable() {
            $query= "DROP TABLE IF EXISTS `users_db`.`temp`";
            $handle= $this->conn->query($query);
            if(!$handle) {
                die("Failed to perform delete: go back and try again");
            }
        }

    }