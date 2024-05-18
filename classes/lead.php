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
    public function setBusinessName($businessName): void
    {
        $this->_businessName = $businessName;
    }
    public function getBusinessName(): string
    {
    return $this->_businessName;
    }
}