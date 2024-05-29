<?php

/**
 * The Contact class defines a Contact object that can be used to
 * represent form submissions for the "Contact Us" form.
 *
 * @author Tien Han <tienthuyhan@gmail.com>
 * @date   5/29/2024
 */

class Contact
{
    private $_firstName;
    private $_lastName;
    private $_email;
    private $_concern;

    public function __construct($firstName, $lastName, $email, $concern)
    {
        $this->_firstName = $firstName;
        $this->_lastName = $lastName;
        $this->_email = $email;
        $this->_concern = $concern;
    }
}