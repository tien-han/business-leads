<?php
    /*
     * @author Tien Han
     * @date 5/17/2024
     * @description Validation for form responses.
     */

    //Turn on error reporting
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    //------------------------ Form Validation Methods ------------------------//
    //Check to see that an email address is valid for logging in
    function validateEmail($email): bool {
        //Remove all illegal characters from email and see if it's a valid email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $isEmail = filter_var($email, FILTER_VALIDATE_EMAIL);

        //Validate that the email ends in "ups.com"
        $upsDomain = 'ups.com';
        $emailDomain = explode('@', $email, 2);
        if ($emailDomain[1] == $upsDomain) {
            $isUPS = true;
        } else {
            $isUPS = false;
        }

        return $isEmail AND $isUPS;
    }

    //Check to see that a password is valid for logging in
    function validatePassword($password): bool {
        //Regex to check that the password has at least:
        //    - An uppercase
        //    - A lowercase
        //    - A numerical value
        //    - A special character
        //    - 8 characters long
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

        return preg_match($pattern, $password);
    }
//-------------------MAIN FORM VALIDATION--------------------------------//
//checks that the name contains all letters
//and is greater than or equal to 2 letters
function validName($name){
    return (ctype_alpha($name) && strlen($name) >= 2);
}
//needs work :'(
function validAddress($address){
        if(!empty($address)) {
            $check_pattern = '/\d+ [0-9a-zA-Z ]+/';
            return !preg_match($check_pattern, $address);
        }
        return false;
}
//checks if the phone number is greater than 10
//digits and contains all numbers
function validPhone($phone){
   return strlen($phone)===10 && is_numeric($phone);
}