<?php
class User
{
    private $_firstName;
    private $_lastName;
    private $_email;
    private $_slic;
    private $_role;
    private $_password;

    public function __construct()
    {
        $this->_firstName = "unknown";
        $this->_lastName = "unknown";
        $this->_email = "unknown";
        $this->_slic = "unknown";
        $this->_role = "unknown";
        $this->_password = "unknown";
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
        return $this->_firstName;
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