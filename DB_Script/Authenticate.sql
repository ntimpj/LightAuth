-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 13, 2020 at 02:21 PM
-- Server version: 10.3.22-MariaDB-1ubuntu1-log
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Authenticate`
--

-- --------------------------------------------------------

--
-- Table structure for table `EmailVerification`
--

CREATE TABLE `EmailVerification` (
  `id` int(11) NOT NULL,
  `verificationid` char(40) NOT NULL,
  `email` varchar(255) NOT NULL,
  `issuedate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Login`
--

CREATE TABLE `Login` (
  `id` int(11) NOT NULL COMMENT 'The ID of this login',
  `userid` int(11) NOT NULL COMMENT 'The ID of the logedin user',
  `token` char(40) NOT NULL COMMENT 'The token to be used to authenticate',
  `tokendate` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'The date time when token was issued',
  `renewtoken` char(40) NOT NULL COMMENT 'Token to renew login'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------


--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `id` int(11) NOT NULL COMMENT 'UserID',
  `firstname` varchar(255) DEFAULT NULL COMMENT 'First Name',
  `lastname` varchar(255) DEFAULT NULL COMMENT 'Last Name',
  `email` varchar(255) NOT NULL COMMENT 'E Mail',
  `emailverified` tinyint(1) NOT NULL COMMENT 'Email verified',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Phone number',
  `phoneverified` tinyint(1) NOT NULL COMMENT 'Phone verified',
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `birthdate` date DEFAULT NULL COMMENT 'Birthdate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for table `EmailVerification`
--
ALTER TABLE `EmailVerification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Login`
--
ALTER TABLE `Login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserID` (`userid`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `EmailVerification`
--
ALTER TABLE `EmailVerification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `Login`
--
ALTER TABLE `Login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The ID of this login', AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'UserID', AUTO_INCREMENT=1;


--
-- Constraints for table `Login`
--
ALTER TABLE `Login`
  ADD CONSTRAINT `Login_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
