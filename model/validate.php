<?php
/**
 * @description Validation for form responses.
 * @author Tien Han, Garrett Ballreich, Sage Markwardt
 * @date 6/3/2024
 */

//------------------------ Form Validation Methods ------------------------//

class Validate
{
    /**
     * Checks that the given string only contains alphabetic values
     * and has at least 2 letters.
     *
     * @param string $name given string name to validate
     * @return bool true if name is valid, false if not
     */
    static function validateName($name): bool
    {
        //We're not using ctype_alpha($name) because it doens't allow spaces, and we use this
        //method for validating "business name", which may have spaces.
        return (strlen($name) >= 2 && preg_match('/^[a-zA-Z ]+$/', $name));
    }

    /**
     * Checks that the given email address is valid
     *
     * @param string $email given email to validate
     * @return bool true if email is valid, false if not
     */
    static function validateEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Checks that the given email address is valid and with UPS
     *
     * @param string $email given email to validate
     * @return bool true if email is valid, false if not
     */
    static function validateUPSEmail($email): bool
    {
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

        return $isEmail and $isUPS;
    }

    /**
     * Checks that the given password is valid when signing up. Criteria as listed:
     *   At least
     *     - An uppercase
     *     - A lowercase
     *     - A numerical value
     *     - A special character
     *     - At 8 characters long
     *
     * @param string $password given string password to validate
     * @return bool true if password is valid, false if not
     */
    static function validatePassword($password): bool
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        return preg_match($pattern, $password);
    }

//needs work :'(
    static function validAddress($address)
    {
        if (!empty($address)) {
            $check_pattern = '/\d+ [0-9a-zA-Z ]+/';
            return !preg_match($check_pattern, $address);
        }
        return false;
    }
//checks if the phone number is greater than 10
//digits and contains all numbers
    static function validPhone($phone)
    {
        return strlen($phone) === 10 && is_numeric($phone);
    }


//check that the employee ID is all numeric
//and a length of 7 digits
    static function validEmployeeID($ID)
    {
        return strlen($ID) === 7 && is_numeric($ID);
    }
//check that the slic is 4 digits long
//and only contains numbers
    static function validSlic($slic)
    {
        return strlen($slic) === 4 && is_numeric($slic);
    }

    //-------------------SIGN UP FORM VALIDATION--------------------------------//
    static function validRole($role)
    {
        return ($role === "CM" || $role == "BD" || $role == "DM");
    }
}