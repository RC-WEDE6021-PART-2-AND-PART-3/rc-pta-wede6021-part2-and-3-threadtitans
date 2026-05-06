-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 10:48 AM
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
-- Database: `pastimesdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblcart`
--

DROP TABLE IF EXISTS `tblcart`;
CREATE TABLE IF NOT EXISTS `tblcart` (
  `CartID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) DEFAULT 1,
  `AddedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`CartID`),
  KEY `UserID` (`UserID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcategories`
--

DROP TABLE IF EXISTS `tblcategories`;
CREATE TABLE IF NOT EXISTS `tblcategories` (
  `CategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(50) NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategories`
--

INSERT INTO `tblcategories` (`CategoryID`, `CategoryName`) VALUES
(1, 'Shirts'),
(2, 'Pants/Jeans'),
(3, 'Jackets'),
(4, 'Shoes'),
(5, 'Accessories'),
(6, 'Dresses'),
(7, 'Suits'),
(8, 'Bags');

-- --------------------------------------------------------

--
-- Table structure for table `tblmessages`
--

DROP TABLE IF EXISTS `tblmessages`;
CREATE TABLE IF NOT EXISTS `tblmessages` (
  `MessageID` int(11) NOT NULL AUTO_INCREMENT,
  `SenderID` int(11) NOT NULL,
  `ReceiverID` int(11) NOT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Message` text NOT NULL,
  `IsRead` enum('no','yes') DEFAULT 'no',
  `SentAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MessageID`),
  KEY `SenderID` (`SenderID`),
  KEY `ReceiverID` (`ReceiverID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblorderitems`
--

DROP TABLE IF EXISTS `tblorderitems`;
CREATE TABLE IF NOT EXISTS `tblorderitems` (
  `OrderItemID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`OrderItemID`),
  KEY `OrderID` (`OrderID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorderitems`
--

INSERT INTO `tblorderitems` (`OrderItemID`, `OrderID`, `ProductID`, `Quantity`, `Price`) VALUES
(1, 1, 2, 3, 45.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblorders`
--

DROP TABLE IF EXISTS `tblorders`;
CREATE TABLE IF NOT EXISTS `tblorders` (
  `OrderID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `OrderDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `TotalAmount` decimal(10,2) NOT NULL,
  `ShippingAddress` text NOT NULL,
  `PaymentMethod` enum('credit_card','paypal','bank_transfer','cod') DEFAULT 'credit_card',
  `OrderStatus` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  PRIMARY KEY (`OrderID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorders`
--

INSERT INTO `tblorders` (`OrderID`, `UserID`, `OrderDate`, `TotalAmount`, `ShippingAddress`, `PaymentMethod`, `OrderStatus`) VALUES
(1, 4, '2026-05-04 23:20:31', 135.00, '12345 ABC STREET', 'credit_card', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `tblproducts`
--

DROP TABLE IF EXISTS `tblproducts`;
CREATE TABLE IF NOT EXISTS `tblproducts` (
  `ProductID` int(11) NOT NULL AUTO_INCREMENT,
  `SellerID` int(11) NOT NULL,
  `ProductName` varchar(100) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `OriginalPrice` decimal(10,2) DEFAULT NULL,
  `Description` text NOT NULL,
  `Condition` enum('new','like-new','good','fair') DEFAULT 'good',
  `Size` varchar(20) DEFAULT NULL,
  `Brand` varchar(50) DEFAULT NULL,
  `ImagePath` varchar(255) DEFAULT NULL,
  `Status` enum('pending','approved','rejected','sold') DEFAULT 'pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ProductID`),
  KEY `SellerID` (`SellerID`),
  KEY `CategoryID` (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblproducts`
--

INSERT INTO `tblproducts` (`ProductID`, `SellerID`, `ProductName`, `CategoryID`, `Price`, `OriginalPrice`, `Description`, `Condition`, `Size`, `Brand`, `ImagePath`, `Status`, `CreatedAt`) VALUES
(1, 2, 'Nike Air Max 90', 4, 89.99, 189.99, 'Excellent condition Nike sneakers, worn only twice', 'like-new', 'US 10', 'Nike', NULL, 'approved', '2026-05-04 22:28:24'),
(2, 2, 'Levi\'s 501 Jeans', 2, 45.00, 120.00, 'Classic Levi\'s jeans, great condition', 'good', '32x32', 'Levi\'s', NULL, 'sold', '2026-05-04 22:28:24'),
(3, 2, 'Supreme T-Shirt', 1, 65.00, 150.00, 'Limited edition Supreme tee', 'like-new', 'M', 'Supreme', NULL, 'approved', '2026-05-04 22:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

DROP TABLE IF EXISTS `tblusers`;
CREATE TABLE IF NOT EXISTS `tblusers` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `UserType` enum('buyer','seller','admin') DEFAULT 'buyer',
  `IsVerified` enum('pending','verified','rejected') DEFAULT 'pending',
  `SellerVerification` enum('pending','approved','rejected') DEFAULT 'pending',
  `Phone` varchar(20) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`UserID`, `Username`, `FullName`, `Email`, `PasswordHash`, `UserType`, `IsVerified`, `SellerVerification`, `Phone`, `Address`, `CreatedAt`) VALUES
(1, 'admin', 'Administrator', 'admin@pastimes.com', '0192023a7bbd73250516f069df18b500', 'admin', 'verified', 'approved', NULL, NULL, '2026-05-04 22:28:24'),
(2, 'john_seller', 'John Seller', 'john@example.com', '482c811da5d5b4bc6d497ffa98491e38', 'seller', 'verified', 'approved', NULL, NULL, '2026-05-04 22:28:24'),
(3, 'jane_buyer', 'Jane Buyer', 'jane@example.com', '482c811da5d5b4bc6d497ffa98491e38', 'buyer', 'verified', 'pending', NULL, NULL, '2026-05-04 22:28:24'),
(4, 'Rata', 'Ratanang', 'ratam@testing.com', '54bb7529f213760c514ca409858503ec', 'buyer', 'verified', 'pending', '0918276654', NULL, '2026-05-04 23:09:38'),
(5, 'amok', 'Amo K', 'amok@testing.com', '0975000de6760d067e4ffc9e408564e0', 'seller', 'pending', 'pending', '0917654432', NULL, '2026-05-04 23:14:04'),
(7, 'K Zam', 'Kenneth', 'KZam@testing.com', '46e9b6a8a689eda1b39f5970cbb0b226', 'buyer', 'verified', 'pending', '0916774561', NULL, '2026-05-05 21:45:24');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblcart`
--
ALTER TABLE `tblcart`
  ADD CONSTRAINT `tblcart_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `tblusers` (`UserID`),
  ADD CONSTRAINT `tblcart_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `tblproducts` (`ProductID`);

--
-- Constraints for table `tblmessages`
--
ALTER TABLE `tblmessages`
  ADD CONSTRAINT `tblmessages_ibfk_1` FOREIGN KEY (`SenderID`) REFERENCES `tblusers` (`UserID`),
  ADD CONSTRAINT `tblmessages_ibfk_2` FOREIGN KEY (`ReceiverID`) REFERENCES `tblusers` (`UserID`),
  ADD CONSTRAINT `tblmessages_ibfk_3` FOREIGN KEY (`ProductID`) REFERENCES `tblproducts` (`ProductID`);

--
-- Constraints for table `tblorderitems`
--
ALTER TABLE `tblorderitems`
  ADD CONSTRAINT `tblorderitems_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `tblorders` (`OrderID`),
  ADD CONSTRAINT `tblorderitems_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `tblproducts` (`ProductID`);

--
-- Constraints for table `tblorders`
--
ALTER TABLE `tblorders`
  ADD CONSTRAINT `tblorders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `tblusers` (`UserID`);

--
-- Constraints for table `tblproducts`
--
ALTER TABLE `tblproducts`
  ADD CONSTRAINT `tblproducts_ibfk_1` FOREIGN KEY (`SellerID`) REFERENCES `tblusers` (`UserID`),
  ADD CONSTRAINT `tblproducts_ibfk_2` FOREIGN KEY (`CategoryID`) REFERENCES `tblcategories` (`CategoryID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
