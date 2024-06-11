<?php
/**
 * this class represents a lead object that will
 * be added the database
 *
 * @author Tien Han, Garrett Ballreich <tienthuyhan@gmail.com> <garrett.ballreich101@gmail.com>
 * @date   5/29/2024
 */
class Lead
{
    //lead fields
    private $_businessName;
    private $_businessAddress;
    private $_contactName;
    private $_contactLastName;
    private $_businessPhone;
    private $_contactEmail;
    private $_driverName;
    private $_driverID;
    private $_slic;

    /**
     * lead constructor, assigns all fields to
     * an unknown default type
     */
    public function __construct()
    {
        $this->_businessName = "unknown";
        $this->_businessAddress = "unknown";
        $this->_contactName = "unknown";
        $this->_contactLastName = "unknown";
        $this->_businessPhone = "unknown";
        $this->_contactEmail = "unknown";
        $this->_driverName = "unknown";
        $this->_driverID = "unknown";
        $this->_slic = "unknown";
    }
    /**
     * @param $businessName
     * @return void
     * sets the business name
     */
    public function setBusinessName($businessName): void
    {
        $this->_businessName = $businessName;
    }

    /**
     * @return string
     * returns the business name
     */
    public function getBusinessName(): string
    {
    return $this->_businessName;
    }
    /**
     * @param $businessAddress
     * @return void
     * sets the business address
     */
    public function setBusinessAddress($businessAddress): void
    {
        $this->_businessAddress = $businessAddress;
    }

    /**
     * @return string
     * returns the business address
     */
    public function getBusinessAddress(): string
    {
        return $this->_businessAddress;
    }
    /**
     * @param $contactName
     * @return void
     * sets the contact name
     */
    public function setContactName($contactName): void
    {
        $this->_contactName = $contactName;
    }

    /**
     * @return string
     * returns the contact name
     */
    public function getContactName(): string
    {
        return $this->_contactName;
    }
    /**
     * @param $contactLastName
     * @return void
     * sets the contact last name
     */
    public function setContactLastName($contactLastName): void
    {
        $this->_contactLastName = $contactLastName;
    }

    /**
     * @return string
     * returns the contact last name
     */
    public function getContactLastName(): string
    {
        return $this->_contactLastName;
    }
    /**
     * @param $businessPhone
     * @return void
     * sets the business phone number
     */
    public function setBusinessPhone($businessPhone): void
    {
        $this->_businessPhone = $businessPhone;
    }

    /**
     * @return string
     * returns the business phone number
     */
    public function getBusinessPhone(): string
    {
        return $this->_businessPhone;
    }
    /**
     * @param $contactEmail
     * @return void
     * sets the contact email
     */
    public function setContactEmail($contactEmail): void
    {
        $this->_contactEmail = $contactEmail;
    }

    /**
     * @return string
     * returns the contact email
     */
    public function getContactEmail(): string
    {
        return $this->_contactEmail;
    }
    /**
     * @param $driverName
     * @return void
     * sets the drivers name
     */
    public function setDriverName($driverName): void
    {
        $this->_driverName = $driverName;
    }

    /**
     * @return string
     * returns the drivers name
     */
    public function getDriverName(): string
    {
        return $this->_driverName;
    }
    /**
     * @param $driverID
     * @return void
     * sets the drivers ID
     */
    public function setDriverID($driverID): void
    {
        $this->_driverID = $driverID;
    }
    /**
     * @return string
     * returns the drivers ID
     */
    public function getDriverID(): string
    {
        return $this->_driverID;
    }
    /**
     * @param $slic
     * @return void
     * sets the slic
     */
    public function setSlic($slic): void
    {
        $this->_slic = $slic;
    }
    /**
     * @return string
     * returns the slic
     */
    public function getSlic(): string
    {
        return $this->_slic;
    }
}