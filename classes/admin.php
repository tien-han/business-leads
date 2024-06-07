<?php
/**
 * The Admin class defines an Admin and methods to get/set user details (from the parent
 * user class).
 *
 * @author Garrett Ballreich, Tien Han <tienthuyhan@gmail.com>
 * @date   6/7/2024
 */

class Admin extends User
{
    //--------------------------------Private Fields--------------------------------
    private bool $_canDeleteLead;

    /**
     * The constructor for the Admin class, this represents an admin with the additional ability
     * to delete leads.
     *
     * @param string $firstName the user's first name
     * @param string $lastName the users last name
     * @param string $email the user's email
     * @param int $slic the user's SLIC #
     * @param string $role the user's role in the application
     * @param bool $accessApproved whether the user's access has been approved or not
     */
    public function __construct(string $firstName="unknown", string $lastName="unknown", string $email="unknown",
                                int $slic=0, string $role="unknown", bool $accessApproved=false,
                                bool $canDeleteLead=true)
    {
        parent::__construct($firstName, $lastName, $email, $slic, $role, $accessApproved);
        $this->_canDeleteLead = $canDeleteLead;
    }

    //--------------------------------Getters and Setters--------------------------------
    /**
     * Set that the user can delete business leads
     *
     * @param bool $canDelete if the user's access is approved
     *
     * @return void
     */
    public function setCanDeleteLead(bool $canDelete): void
    {
        $this->_canDeleteLead = $canDelete;
    }

    /**
     * Get whether the user can delete business leads
     *
     * @return bool whether the can delete business leads or not
     */
    public function getCanDeleteLead(): bool
    {
        return $this->_canDeleteLead;
    }
}