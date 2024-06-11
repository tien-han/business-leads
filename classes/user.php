<?php
/**
 * The User class defines a User and methods to get/set user details.
 *
 * @author Garrett Ballreich, Tien Han <tienthuyhan@gmail.com>, Sage Markwardt
 * @date   6/11/2024
 */

class User
{
    //--------------------------------Private Fields--------------------------------
    private string $_firstName;
    private string $_lastName;
    private string $_email;
    private int $_slic;
    private string $_role;
    private bool $_accessApproved;

    /**
     * The constructor for the User class, this represents a "base" user that we can inherit from.
     *
     * @param string $firstName the user's first name
     * @param string $lastName the users last name
     * @param string $email the user's email
     * @param int $slic the user's SLIC #
     * @param string $role the user's role in the application
     * @param bool $accessApproved whether the user's access has been approved or not
     */
    public function __construct(string $firstName="unknown", string $lastName="unknown", string $email="unknown",
                                int $slic=0, string $role="unknown", bool $accessApproved=false)
    {
        $this->_firstName = $firstName;
        $this->_lastName = $lastName;
        $this->_email = $email;
        $this->_slic = $slic;
        $this->_role = $role;
        $this->_accessApproved = $accessApproved;
    }

    //--------------------------------Getters and Setters--------------------------------

    /**
     * Set the user's first name
     *
     * @param string $firstName the user's new first name
     *
     * @return void
     */
    public function setFirstName(string $firstName): void
    {
        $this->_firstName = $firstName;
    }

    /**
     * Get the user's first name
     *
     * @return string the user's first name
     */
    public function getFirstName(): string
    {
        return $this->_firstName;
    }

    /**
     * Set the user's last name
     *
     * @param string $lastName the user's new last name
     *
     * @return void
     */
    //last name
    public function setLastName(string $lastName): void
    {
        $this->_lastName = $lastName;
    }

    /**
     * Get the user's last name
     *
     * @return string the user's last name
     */
    public function getLastName(): string
    {
        return $this->_lastName;
    }

    /**
     * Set the user's email
     *
     * @param string $email the user's new email
     *
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->_email = $email;
    }

    /**
     * Get the user's email
     *
     * @return string the user's email
     */
    public function getEmail(): string
    {
        return $this->_email;
    }

    /**
     * Set the user's SLIC number
     *
     * @param int $slic the user's new SLIC number
     *
     * @return void
     */
    public function setSlic(int $slic): void
    {
        $this->_slic = $slic;
    }

    /**
     * Get the user's SLIC
     *
     * @return int the user's SLIC number
     */
    public function getSlic(): int
    {
        return $this->_slic;
    }

    /**
     * Set the user's role
     *
     * @param string $role the user's new role
     *
     * @return void
     */
    public function setRole(string $role): void
    {
        $this->_role = $role;
    }

    /**
     * Get the user's role
     *
     * @return string the user's role
     */
    public function getRole(): string
    {
        return $this->_role;
    }

    /**
     * Set the user's access (if they were approved or not)
     *
     * @param bool $approved if the user's access is approved
     *
     * @return void
     */
    public function setAccessApproved(bool $approved): void
    {
        $this->_accessApproved = $approved;
    }

    /**
     * Get whether the user's access has been approved
     *
     * @return bool whether the user's access has been approved or not
     */
    public function getAccessApproved(): bool
    {
        return $this->_accessApproved;
    }
}