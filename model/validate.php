<?php
    /*
     * @author Tien Han
     * @date 5/8/2024
     * @description Validation for form responses.
     */

    //Turn on error reporting
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    //------------------------ Form Validation Methods ------------------------//
    //Check to see that an email address is valid for logging in
    function validEmail($email): bool {
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