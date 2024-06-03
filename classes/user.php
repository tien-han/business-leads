<?php
/**
 * The User class defines a User and methods to get/set user details.
 *
 * @author Garrett Ballreich, Tien Han <tienthuyhan@gmail.com>
 * @date   6/3/2024
 */
class User
{
    private $_firstName;
    private $_lastName;
    private $_email;
    private $_slic;
    private $_role;
    private $_password;

    public function __construct($firstName="unknown", $lastName="unknown", $email="unknown",
                                $slic="unknown", $role="unknown", $password="unknown")
    {
        $this->_firstName = $firstName;
        $this->_lastName = $lastName;
        $this->_email = $email;
        $this->_slic = $slic;
        $this->_role = $role;
        $this->_password = $password;
    }
    //setters and getters
    //first name
    public function setFirstName($firstName): void
    {
        $this->_firstName = $firstName;
    }
    public function getFirstName(): string
    {
        return $this->_firstName;
    }
    //last name
    public function setLastName($lastName): void
    {
        $this->_lastName = $lastName;
    }
    public function getLastName(): string
    {
        return $this->_lastName;
    }
    //email
    public function setEmail($email): void
    {
        $this->_email = $email;
    }
    public function getEmail(): string
    {
        return $this->_email;
    }
    //slic
    public function setSlic($slic): void
    {
        $this->_slic = $slic;
    }
    public function getSlic(): string
    {
        return $this->_slic;
    }
    //role
    public function setRole($role): void
    {
        $this->_role = $role;
    }
    public function getRole(): string
    {
        return $this->_role;
    }
    //password
    public function setPassword($password): void
    {
        $this->_password = $password;
    }
    public function getPassword(): string
    {
        return $this->_password;
    }
}