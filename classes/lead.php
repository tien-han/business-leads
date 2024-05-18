<?php

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

    //lead constructor
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

    //getters and setters for fields
    //business name
    public function setBusinessName($businessName): void
    {
        $this->_businessName = $businessName;
    }
    public function getBusinessName(): string
    {
    return $this->_businessName;
    }
    //business address
    public function setBusinessAddress($businessAddress): void
    {
        $this->_businessAddress = $businessAddress;
    }
    public function getBusinessAddress(): string
    {
        return $this->_businessAddress;
    }
    //contact name
    public function setContactName($contactName): void
    {
        $this->_contactName = $contactName;
    }
    public function getContactName(): string
    {
        return $this->_contactName;
    }
    //contact last name
    public function setContactLastName($contactLastName): void
    {
        $this->_contactLastName = $contactLastName;
    }
    public function getContactLastName(): string
    {
        return $this->_contactLastName;
    }
    //business phone
    public function setBusinessPhone($businessPhone): void
    {
        $this->_businessPhone = $businessPhone;
    }
    public function getBusinessPhone(): string
    {
        return $this->_businessPhone;
    }
    //contact email
    public function setContactEmail($contactEmail): void
    {
        $this->_contactEmail = $contactEmail;
    }
    public function getContactEmail(): string
    {
        return $this->_contactEmail;
    }
    //driver name
    public function setDriverName($driverName): void
    {
        $this->_driverName = $driverName;
    }
    public function getDriverName(): string
    {
        return $this->_driverName;
    }
    //driver ID
    public function setDriverID($driverID): void
    {
        $this->_driverID = $driverID;
    }
    public function getDriverID(): string
    {
        return $this->_driverID;
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
}