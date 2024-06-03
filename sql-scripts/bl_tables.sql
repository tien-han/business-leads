-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 28, 2014 at 11:13 AM
-- Server version: 5.1.73
-- PHP Version: 5.3.2-1ubuntu4.27
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
--
-- Database: `business-leads`
--
-- Drop existing tables
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS leads;
DROP TABLE IF EXISTS slics;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS password_reset_temp;

-- --------------------------------------------------------
--
-- Table structure for table `users`
--
CREATE TABLE IF NOT EXISTS `users`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(255) DEFAULT NULL,
    `last_name` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `password` VARCHAR(20) DEFAULT NULL,
    `account_activated` BOOLEAN DEFAULT FALSE,
    `account_activated_at` DATETIME DEFAULT NULL,
    `role` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `deleted` BOOLEAN DEFAULT FALSE,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
--
-- Dumping data for table `users`
--
INSERT INTO `users`(
    `first_name`,
    `last_name`,
    `email`,
    `account_activated`,
    `role`
)
VALUES (
    'Annie',
    'Appletree',
    'annie@ups.com',
    true,
    'Center Manager'
), (
    'Miedrail',
    'Pelilde',
    'm.pelilde@ups.com',
    false,
    'Division Manager'
);

-- --------------------------------------------------------
--
-- Table structure for table `slics`
--
CREATE TABLE IF NOT EXISTS `slics`(
    `id` INT NOT NULL PRIMARY KEY, #Corresponds to actual SLIC ids at UPS
    `name` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `deleted` BOOLEAN DEFAULT FALSE,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
--
-- Dumping data for table `slics`
--
INSERT INTO `slics`(
    `id`,
    `name`,
    `created_at`
)
VALUES (
    9701,
    'THE DALLES',
    NOW()
), (
    9782,
    'LA GRANDE',
    NOW()
), (
    9801,
    'TUKWILA',
    NOW()
), (
    9890,
    'YAKIMA',
    NOW()
), (
    9930,
    'KENNEWICK',
    NOW()
), (
    8350,
    'LEWISTON',
    NOW()
), (
    9780,
    'PENDLETON',
    NOW()
), (
    9784,
    'ENTERPRISE',
    NOW()
), (
    9891,
    'ELLENSBURG',
    NOW()
), (
    9931,
    'WALLA WALLA',
    NOW()
), (
    8351,
    'GRANGEVILLE',
    NOW()
), (
    9781,
    'BAKER',
    NOW()
), (
    9785,
    'HERMISTON',
    NOW()
), (
    9876,
    'PACIFIC',
    NOW()
), (
    9910,
    'PULLMAN',
    NOW()
);

-- --------------------------------------------------------
--
-- Table structure for table `leads`
--
CREATE TABLE IF NOT EXISTS `leads`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `contact_name` VARCHAR(255) DEFAULT NULL,
    `contact_phone` VARCHAR(10) DEFAULT NULL,
    `contact_email` VARCHAR(255) DEFAULT NULL,
    `ups_employee_name` VARCHAR(255) DEFAULT NULL,
    `ups_employee_id` INT DEFAULT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `last_updated_date` DATETIME DEFAULT NULL,
    `slic` INT DEFAULT NULL,
    `assigned_to` INT DEFAULT NULL,
    FOREIGN KEY (slic) REFERENCES slics(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    `created_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
--
-- Dumping data for table `leads`
--
INSERT INTO `leads`(
    `name`,
    `slic`,
    `assigned_to`
)
VALUES (
    'Google',
    9701,
    2
), (
    'Daiso',
    9782,
    1
);

-- --------------------------------------------------------
--
-- Table structure for table `messages`
--
CREATE TABLE IF NOT EXISTS `messages`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `date` DATETIME DEFAULT NULL,
    `author_id` INT NOT NULL,
    `lead_id` INT NOT NULL,
    `message` VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (author_id) REFERENCES users(id),
    FOREIGN KEY (lead_id) REFERENCES leads(id)
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
--
-- Dumping data for table `messages`
--
INSERT INTO messages(
    `date`,
    `author_id`,
    `lead_id`,
    `message`
) VALUES (
    '2024-05-23 12:32:32',
    1,
    1,
    'This lead is the BEST, please follow up soon!'
), (
    '2024-05-24 12:32:32',
    2,
    1,
    'This lead may be a multi million dollar deal, please contact ASAP.'
);

-- --------------------------------------------------------
--
-- Table structure for table `password_reset_temp`
--
-- Note: This token database is used to reset passwords
-- Dummy data isn't needed as data will be populated by the "reset password" feature
--
CREATE TABLE `password_reset_temp` (
    `email` varchar(250) NOT NULL,
    `key` varchar(250) NOT NULL,
    `expDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;