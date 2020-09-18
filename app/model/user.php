<?php
    class user{
        //protected $name;

        public function db() {
            include Database . 'database.php';
            return new DbController();
        }
        public function sendAuthCode($authCode, $email) {
            //send an authentication code to user's email
            $toEmail= $email;
            $subject= 'User Registration Activation Code';
            $content= $authCode;
            $mailHeaders= 'From: Admin\r\n';

            if(mail($toEmail, $subject, $content, $mailHeaders)){
                return true;
            }
        }
    }