-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2025 at 07:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lendingsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `actualpayments`
--

CREATE TABLE `actualpayments` (
  `TransactionID` int(11) NOT NULL,
  `ScheduledPaymentID` int(11) DEFAULT NULL,
  `LoanID` int(11) NOT NULL,
  `PayerID` int(11) NOT NULL,
  `Amount` decimal(15,2) NOT NULL,
  `PaymentDate` datetime NOT NULL DEFAULT current_timestamp(),
  `PaymentMethod` varchar(50) NOT NULL,
  `ReferenceNumber` varchar(100) DEFAULT NULL,
  `Notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `actualpayments`
--

INSERT INTO `actualpayments` (`TransactionID`, `ScheduledPaymentID`, `LoanID`, `PayerID`, `Amount`, `PaymentDate`, `PaymentMethod`, `ReferenceNumber`, `Notes`) VALUES
(1, 1, 1, 1, 315.32, '2025-02-18 14:36:54', 'bank transfer', 'BT-123456', NULL),
(2, 2, 1, 1, 315.32, '2025-02-18 14:36:54', 'bank transfer', 'BT-123457', NULL),
(3, 4, 2, 2, 583.88, '2025-02-18 14:36:54', 'credit card', 'CC-789012', NULL),
(4, 7, 4, 4, 948.11, '2025-02-18 14:36:54', 'automatic debit', 'AD-456789', NULL),
(5, 8, 4, 4, 948.11, '2025-02-18 14:36:54', 'automatic debit', 'AD-456790', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `auditlogs`
--

CREATE TABLE `auditlogs` (
  `LogID` int(11) NOT NULL,
  `TableName` varchar(50) NOT NULL,
  `RecordID` int(11) NOT NULL,
  `Action` varchar(20) NOT NULL,
  `ChangedBy` int(11) NOT NULL,
  `ChangedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `OldValues` text DEFAULT NULL,
  `NewValues` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loandocuments`
--

CREATE TABLE `loandocuments` (
  `DocumentID` int(11) NOT NULL,
  `LoanID` int(11) NOT NULL,
  `DocumentType` varchar(50) NOT NULL,
  `FileName` varchar(100) NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `UploadDate` datetime NOT NULL DEFAULT current_timestamp(),
  `UploadedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loannotes`
--

CREATE TABLE `loannotes` (
  `NoteID` int(11) NOT NULL,
  `LoanID` int(11) NOT NULL,
  `CreatedBy` int(11) NOT NULL,
  `CreationDate` datetime NOT NULL DEFAULT current_timestamp(),
  `NoteText` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `LoanID` int(11) NOT NULL,
  `BorrowerID` int(11) NOT NULL,
  `LenderID` int(11) DEFAULT NULL,
  `LoanTypeID` int(11) NOT NULL,
  `Amount` decimal(15,2) NOT NULL,
  `InterestRate` decimal(5,2) NOT NULL,
  `Term` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Purpose` varchar(200) DEFAULT NULL,
  `Status` varchar(20) NOT NULL DEFAULT 'pending',
  `CreationDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`LoanID`, `BorrowerID`, `LenderID`, `LoanTypeID`, `Amount`, `InterestRate`, `Term`, `StartDate`, `EndDate`, `Purpose`, `Status`, `CreationDate`) VALUES
(1, 1, 3, 1, 10000.00, 8.50, 36, '2024-01-15', '2027-01-15', 'Home renovation', 'active', '2025-02-18 14:36:54'),
(2, 2, 4, 2, 25000.00, 5.75, 48, '2024-02-10', '2028-02-10', 'New car purchase', 'active', '2025-02-18 14:36:54'),
(3, 3, 1, 4, 15000.00, 6.25, 60, '2024-03-01', '2029-03-01', 'Graduate school', 'active', '2025-02-18 14:36:54'),
(4, 4, 2, 3, 200000.00, 3.95, 360, '2024-01-20', '2054-01-20', 'Primary residence purchase', 'active', '2025-02-18 14:36:54');

-- --------------------------------------------------------

--
-- Table structure for table `loantypes`
--

CREATE TABLE `loantypes` (
  `LoanTypeID` int(11) NOT NULL,
  `TypeName` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL,
  `MinAmount` decimal(15,2) NOT NULL,
  `MaxAmount` decimal(15,2) NOT NULL,
  `MinInterestRate` decimal(5,2) NOT NULL,
  `MaxInterestRate` decimal(5,2) NOT NULL,
  `MinTerm` int(11) NOT NULL,
  `MaxTerm` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loantypes`
--

INSERT INTO `loantypes` (`LoanTypeID`, `TypeName`, `Description`, `MinAmount`, `MaxAmount`, `MinInterestRate`, `MaxInterestRate`, `MinTerm`, `MaxTerm`, `IsActive`) VALUES
(1, 'Personal Loan', 'General purpose personal loan', 1000.00, 50000.00, 5.99, 24.99, 12, 60, 1),
(2, 'Auto Loan', 'For purchasing new or used vehicles', 5000.00, 100000.00, 3.99, 12.99, 24, 84, 1),
(3, 'Mortgage', 'Home loan for purchasing property', 50000.00, 1000000.00, 2.99, 7.99, 60, 360, 1),
(4, 'Education Loan', 'For funding education expenses', 2000.00, 200000.00, 4.99, 14.99, 36, 180, 1);

-- --------------------------------------------------------

--
-- Table structure for table `paymentschedules`
--

CREATE TABLE `paymentschedules` (
  `ScheduleID` int(11) NOT NULL,
  `LoanID` int(11) NOT NULL,
  `PaymentAmount` decimal(15,2) NOT NULL,
  `PaymentFrequency` varchar(20) NOT NULL,
  `TotalPayments` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paymentschedules`
--

INSERT INTO `paymentschedules` (`ScheduleID`, `LoanID`, `PaymentAmount`, `PaymentFrequency`, `TotalPayments`) VALUES
(1, 1, 315.32, 'monthly', 36),
(2, 2, 583.88, 'monthly', 48),
(3, 3, 292.92, 'monthly', 60),
(4, 4, 948.11, 'monthly', 360);

-- --------------------------------------------------------

--
-- Table structure for table `scheduledpayments`
--

CREATE TABLE `scheduledpayments` (
  `PaymentID` int(11) NOT NULL,
  `ScheduleID` int(11) NOT NULL,
  `DueDate` date NOT NULL,
  `Amount` decimal(15,2) NOT NULL,
  `PrincipalAmount` decimal(15,2) NOT NULL,
  `InterestAmount` decimal(15,2) NOT NULL,
  `Status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduledpayments`
--

INSERT INTO `scheduledpayments` (`PaymentID`, `ScheduleID`, `DueDate`, `Amount`, `PrincipalAmount`, `InterestAmount`, `Status`) VALUES
(1, 1, '2024-02-15', 315.32, 244.99, 70.33, 'paid'),
(2, 1, '2024-03-15', 315.32, 246.71, 68.61, 'paid'),
(3, 1, '2024-04-15', 315.32, 248.45, 66.87, 'pending'),
(4, 2, '2024-03-10', 583.88, 502.80, 81.08, 'paid'),
(5, 2, '2024-04-10', 583.88, 505.20, 78.68, 'pending'),
(6, 3, '2024-04-01', 292.92, 230.42, 62.50, 'pending'),
(7, 4, '2024-02-20', 948.11, 281.61, 666.50, 'paid'),
(8, 4, '2024-03-20', 948.11, 282.70, 665.41, 'paid'),
(9, 4, '2024-04-20', 948.11, 283.80, 664.31, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Address` varchar(200) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `CreditScore` int(11) DEFAULT NULL,
  `RegistrationDate` datetime NOT NULL DEFAULT current_timestamp(),
  `UserStatus` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FirstName`, `LastName`, `Email`, `Password`, `Phone`, `Address`, `DateOfBirth`, `CreditScore`, `RegistrationDate`, `UserStatus`) VALUES
(1, 'John', 'Doe', 'john@example.com', '$2y$10$Ke7oUcJQmcYlUo89BgeFouGfjvvGbgolZKw8s9yxr/TvdccAbr.Ta', '555-123-4567', '123 Main St, Anytown', '1980-05-15', 750, '2025-02-18 14:36:54', 'active'),
(2, 'Jane', 'Smith', 'jane@example.com', '$2y$10$SHoYPhPpS6Rb5TH/XMl8O.5tK25M5uYC2RZqDY9e8/QDiGIrXqJdO', '555-765-4321', '456 Oak Ave, Somewhere', '1985-10-22', 680, '2025-02-18 14:36:54', 'active'),
(3, 'Michael', 'Johnson', 'michael@example.com', '$2y$10$JGx9V46YqjxbcgQbCB3bWuoRcSL6JtUnOUjpqWXFJswMjZp.8aLPu', '555-987-6543', '789 Pine St, Nowhere', '1975-03-30', 790, '2025-02-18 14:36:54', 'active'),
(4, 'Sarah', 'Williams', 'sarah@example.com', '$2y$10$dK7hG6JoEw1b.aI5vM1rA.O2VVF5fXZXtRTEEwFgz/ZJTVDf5Qaxe', '555-456-7890', '321 Elm Dr, Anywhere', '1990-12-05', 650, '2025-02-18 14:36:54', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actualpayments`
--
ALTER TABLE `actualpayments`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `ScheduledPaymentID` (`ScheduledPaymentID`),
  ADD KEY `LoanID` (`LoanID`),
  ADD KEY `PayerID` (`PayerID`);

--
-- Indexes for table `auditlogs`
--
ALTER TABLE `auditlogs`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `ChangedBy` (`ChangedBy`);

--
-- Indexes for table `loandocuments`
--
ALTER TABLE `loandocuments`
  ADD PRIMARY KEY (`DocumentID`),
  ADD KEY `LoanID` (`LoanID`),
  ADD KEY `UploadedBy` (`UploadedBy`);

--
-- Indexes for table `loannotes`
--
ALTER TABLE `loannotes`
  ADD PRIMARY KEY (`NoteID`),
  ADD KEY `LoanID` (`LoanID`),
  ADD KEY `CreatedBy` (`CreatedBy`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`LoanID`),
  ADD KEY `BorrowerID` (`BorrowerID`),
  ADD KEY `LenderID` (`LenderID`),
  ADD KEY `LoanTypeID` (`LoanTypeID`);

--
-- Indexes for table `loantypes`
--
ALTER TABLE `loantypes`
  ADD PRIMARY KEY (`LoanTypeID`);

--
-- Indexes for table `paymentschedules`
--
ALTER TABLE `paymentschedules`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `LoanID` (`LoanID`);

--
-- Indexes for table `scheduledpayments`
--
ALTER TABLE `scheduledpayments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `ScheduleID` (`ScheduleID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actualpayments`
--
ALTER TABLE `actualpayments`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `auditlogs`
--
ALTER TABLE `auditlogs`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loandocuments`
--
ALTER TABLE `loandocuments`
  MODIFY `DocumentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loannotes`
--
ALTER TABLE `loannotes`
  MODIFY `NoteID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `LoanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loantypes`
--
ALTER TABLE `loantypes`
  MODIFY `LoanTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `paymentschedules`
--
ALTER TABLE `paymentschedules`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `scheduledpayments`
--
ALTER TABLE `scheduledpayments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `actualpayments`
--
ALTER TABLE `actualpayments`
  ADD CONSTRAINT `actualpayments_ibfk_1` FOREIGN KEY (`ScheduledPaymentID`) REFERENCES `scheduledpayments` (`PaymentID`),
  ADD CONSTRAINT `actualpayments_ibfk_2` FOREIGN KEY (`LoanID`) REFERENCES `loans` (`LoanID`),
  ADD CONSTRAINT `actualpayments_ibfk_3` FOREIGN KEY (`PayerID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `auditlogs`
--
ALTER TABLE `auditlogs`
  ADD CONSTRAINT `auditlogs_ibfk_1` FOREIGN KEY (`ChangedBy`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `loandocuments`
--
ALTER TABLE `loandocuments`
  ADD CONSTRAINT `loandocuments_ibfk_1` FOREIGN KEY (`LoanID`) REFERENCES `loans` (`LoanID`),
  ADD CONSTRAINT `loandocuments_ibfk_2` FOREIGN KEY (`UploadedBy`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `loannotes`
--
ALTER TABLE `loannotes`
  ADD CONSTRAINT `loannotes_ibfk_1` FOREIGN KEY (`LoanID`) REFERENCES `loans` (`LoanID`),
  ADD CONSTRAINT `loannotes_ibfk_2` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`BorrowerID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`LenderID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`LoanTypeID`) REFERENCES `loantypes` (`LoanTypeID`);

--
-- Constraints for table `paymentschedules`
--
ALTER TABLE `paymentschedules`
  ADD CONSTRAINT `paymentschedules_ibfk_1` FOREIGN KEY (`LoanID`) REFERENCES `loans` (`LoanID`);

--
-- Constraints for table `scheduledpayments`
--
ALTER TABLE `scheduledpayments`
  ADD CONSTRAINT `scheduledpayments_ibfk_1` FOREIGN KEY (`ScheduleID`) REFERENCES `paymentschedules` (`ScheduleID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
