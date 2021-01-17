-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 21, 2018 at 08:58 AM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbpis`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblbidders`
--

DROP TABLE IF EXISTS `tblbidders`;
CREATE TABLE IF NOT EXISTS `tblbidders` (
  `bidderID` int(11) NOT NULL AUTO_INCREMENT,
  `classID` int(11) NOT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `contact_person` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `contact_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `mobileNumber` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `establishedDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fileDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emailAddress` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `urlAddress` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `faxNo` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vatNo` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nameBank` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `accountName` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `accountNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `natureBusiness` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0-0-0-0',
  `natureBusinessOthers` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deliveryVehicleNo` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `productLines` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creditAccomodation` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0-0-0-0-0',
  `attachement` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0-0-0-0-0-0-0',
  `attachmentOthers` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`bidderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblbids_quotations`
--

DROP TABLE IF EXISTS `tblbids_quotations`;
CREATE TABLE IF NOT EXISTS `tblbids_quotations` (
  `bidID` int(11) NOT NULL AUTO_INCREMENT,
  `bidderID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `infoID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  `selection` mediumtext COLLATE utf8_unicode_ci,
  `amount` float(10,2) NOT NULL DEFAULT '0.00',
  `lamount` float(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`bidID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblclassifications`
--

DROP TABLE IF EXISTS `tblclassifications`;
CREATE TABLE IF NOT EXISTS `tblclassifications` (
  `classID` int(11) NOT NULL AUTO_INCREMENT,
  `classification` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`classID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblclassifications`
--

INSERT INTO `tblclassifications` (`classID`, `classification`) VALUES
(1, 'Laboratory Supplies, Chemicals and Equipment'),
(2, 'Office Supplies and Materials'),
(3, 'Computer Supplies, Inks, Accessories and Softwares'),
(4, 'Hardware and Construction Supplies'),
(5, 'Printing Services'),
(6, 'Auto Parts and Services'),
(7, 'Labor Services'),
(8, 'Other Assets-From Other Sources'),
(9, 'Foods and Accommodations'),
(10, 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `tblcondemned`
--

DROP TABLE IF EXISTS `tblcondemned`;
CREATE TABLE IF NOT EXISTS `tblcondemned` (
  `wmrID` int(11) NOT NULL AUTO_INCREMENT,
  `storageArea` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `formDate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `certifiedBy` int(11) NOT NULL,
  `approvedBy` int(11) NOT NULL,
  `actionTaken` int(11) NOT NULL,
  `inspectBy` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `witnessBy` int(11) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `conTransferred` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `soldPub` double(25,2) DEFAULT NULL,
  `soldPriv` double(25,2) DEFAULT NULL,
  `finalized` char(1) COLLATE utf8_unicode_ci DEFAULT 'n',
  PRIMARY KEY (`wmrID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbldv`
--

DROP TABLE IF EXISTS `tbldv`;
CREATE TABLE IF NOT EXISTS `tbldv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orsID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dvNo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dvDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paymentMode` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0-0-0-0',
  `particulars` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ors_id` (`orsID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblemp_accounts`
--

DROP TABLE IF EXISTS `tblemp_accounts`;
CREATE TABLE IF NOT EXISTS `tblemp_accounts` (
  `empID` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `sectionID` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `middlename` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lastname` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `position` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_status` tinyint(1) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `blocked` char(1) COLLATE utf8_unicode_ci DEFAULT 'n',
  `picture` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signature` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`empID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblemp_accounts`
--

INSERT INTO `tblemp_accounts` (`empID`, `sectionID`, `firstname`, `middlename`, `lastname`, `position`, `username`, `password`, `user_type`, `login_status`, `last_login`, `blocked`, `picture`, `signature`) VALUES
('MIS', 3, 'Super', '@', 'User', 'MIS', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 0, '2018-04-21 08:55:58', 'n', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbliar`
--

DROP TABLE IF EXISTS `tbliar`;
CREATE TABLE IF NOT EXISTS `tbliar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orsID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iarNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iarDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoiceNo` mediumtext COLLATE utf8_unicode_ci,
  `invoiceDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inspectedBy` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signatorySupply` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `toForm` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblinventory_items`
--

DROP TABLE IF EXISTS `tblinventory_items`;
CREATE TABLE IF NOT EXISTS `tblinventory_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poItemID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `infoID` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inventoryClassNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `propertyNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inventoryClass` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `itemClassification` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stockAvailable` set('yes','no') COLLATE utf8_unicode_ci DEFAULT NULL,
  `estimatedUsefulLife` mediumtext COLLATE utf8_unicode_ci,
  `itemStatus` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'recorded',
  `quantity` int(5) NOT NULL DEFAULT '0',
  `groupNo` int(11) NOT NULL DEFAULT '0',
  `createdAt` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblitem_categories`
--

DROP TABLE IF EXISTS `tblitem_categories`;
CREATE TABLE IF NOT EXISTS `tblitem_categories` (
  `categoryID` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblitem_categories`
--

INSERT INTO `tblitem_categories` (`categoryID`, `category`) VALUES
(1, 'Building'),
(2, 'Furniture & Fixture'),
(3, 'IT Equipment'),
(4, 'Machineries'),
(5, 'Motor Vehicle'),
(6, 'Office Equipment'),
(7, 'Other Assets'),
(8, 'Other Machineries'),
(9, 'Technical & Scientific Equipment');

-- --------------------------------------------------------

--
-- Table structure for table `tblitem_classifications`
--

DROP TABLE IF EXISTS `tblitem_classifications`;
CREATE TABLE IF NOT EXISTS `tblitem_classifications` (
  `classID` int(11) NOT NULL AUTO_INCREMENT,
  `classification` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`classID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblitem_classifications`
--

INSERT INTO `tblitem_classifications` (`classID`, `classification`) VALUES
(1, 'Communication Equipment'),
(2, 'Books'),
(3, 'Office Building'),
(4, 'Office Equipment'),
(5, 'Furnitures and Fixtures'),
(6, 'IT Equipment'),
(7, 'Motor Vehicle'),
(8, 'Medical, Dental, Laboratory Equipment'),
(9, 'Other Property Plant Equipment'),
(10, 'Plant Machinery Equipment'),
(11, 'Office Supply'),
(12, 'Other Expenses'),
(13, 'Other Assets-From Other Sources'),
(14, 'Other Machineries and Equipments'),
(15, 'Medical, Dental, Laboratory Supplies');

-- --------------------------------------------------------

--
-- Table structure for table `tblitem_issue`
--

DROP TABLE IF EXISTS `tblitem_issue`;
CREATE TABLE IF NOT EXISTS `tblitem_issue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventoryID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inventoryClassNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `propertyNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `empID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(5) NOT NULL DEFAULT '0',
  `approvedBy` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `issuedBy` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `issueRemarks` mediumtext COLLATE utf8_unicode_ci,
  `issueDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblitem_subcategories`
--

DROP TABLE IF EXISTS `tblitem_subcategories`;
CREATE TABLE IF NOT EXISTS `tblitem_subcategories` (
  `subID` int(11) NOT NULL AUTO_INCREMENT,
  `catID` int(11) NOT NULL,
  `sub_category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`subID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblors`
--

DROP TABLE IF EXISTS `tblors`;
CREATE TABLE IF NOT EXISTS `tblors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orsNo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poNo` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `serialNo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orsDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payee` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `office` mediumtext COLLATE utf8_unicode_ci,
  `address` mediumtext COLLATE utf8_unicode_ci,
  `particulars` mediumtext COLLATE utf8_unicode_ci,
  `uacsObjectCode` mediumtext COLLATE utf8_unicode_ci,
  `amount` float(10,2) DEFAULT '0.00',
  `signatoryReq` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signatoryReqDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signatoryBudget` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signatoryBudgetDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `documentType` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ors',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpo_jo`
--

DROP TABLE IF EXISTS `tblpo_jo`;
CREATE TABLE IF NOT EXISTS `tblpo_jo` (
  `poNo` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `prID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poApprovalDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `awardedTo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `placeDelivery` mediumtext COLLATE utf8_unicode_ci,
  `deliveryDate` mediumtext COLLATE utf8_unicode_ci,
  `deliveryTerm` mediumtext COLLATE utf8_unicode_ci,
  `paymentTerm` mediumtext COLLATE utf8_unicode_ci,
  `amountWords` mediumtext COLLATE utf8_unicode_ci,
  `totalAmount` float(10,2) NOT NULL DEFAULT '0.00',
  `signatoryApp` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signatoryDept` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signatoryFunds` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `approved` set('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `forApproval` set('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `poStatus` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'for_po',
  PRIMARY KEY (`poNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpo_jo_items`
--

DROP TABLE IF EXISTS `tblpo_jo_items`;
CREATE TABLE IF NOT EXISTS `tblpo_jo_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `infoID` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poNo` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unitIssue` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(5) DEFAULT NULL,
  `itemDescription` mediumtext COLLATE utf8_unicode_ci,
  `amount` float(10,2) NOT NULL DEFAULT '0.00',
  `excluded` set('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpr`
--

DROP TABLE IF EXISTS `tblpr`;
CREATE TABLE IF NOT EXISTS `tblpr` (
  `prID` int(11) NOT NULL AUTO_INCREMENT,
  `prNo` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prApprovalDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `canvassDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abstractDate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abstractApprovalDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `requestBy` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `sectionID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signatory` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `remarks` mediumtext COLLATE utf8_unicode_ci,
  `purpose` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `procurementMode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prStatus` varchar(200) COLLATE utf8_unicode_ci DEFAULT 'pending',
  PRIMARY KEY (`prID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblprocurement_mode`
--

DROP TABLE IF EXISTS `tblprocurement_mode`;
CREATE TABLE IF NOT EXISTS `tblprocurement_mode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modeName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblprocurement_mode`
--

INSERT INTO `tblprocurement_mode` (`id`, `modeName`) VALUES
(1, 'Alternative'),
(2, 'Public Bidding');

-- --------------------------------------------------------

--
-- Table structure for table `tblpr_info`
--

DROP TABLE IF EXISTS `tblpr_info`;
CREATE TABLE IF NOT EXISTS `tblpr_info` (
  `infoID` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `prID` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(5) DEFAULT NULL,
  `unitIssue` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `itemDescription` mediumtext COLLATE utf8_unicode_ci,
  `stockNo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estimateUnitCost` float(10,2) DEFAULT '0.00',
  `estimateTotalCost` float(10,2) DEFAULT '0.00',
  `awardedTo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `awardedRemarks` mediumtext COLLATE utf8_unicode_ci,
  `groupNo` int(5) DEFAULT NULL,
  PRIMARY KEY (`infoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpr_status`
--

DROP TABLE IF EXISTS `tblpr_status`;
CREATE TABLE IF NOT EXISTS `tblpr_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `statusName` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblpr_status`
--

INSERT INTO `tblpr_status` (`id`, `statusName`) VALUES
(1, 'pending'),
(2, 'disapproved'),
(3, 'cancelled'),
(4, 'closed'),
(5, 'for_canvass'),
(6, 'for_po'),
(7, 'obligated'),
(8, 'for_delivery'),
(9, 'for_inspection'),
(10, 'for_disbursement'),
(11, 'for_payment'),
(12, 'recorded'),
(13, 'issued'),
(14, 'condemn');

-- --------------------------------------------------------

--
-- Table structure for table `tblsections`
--

DROP TABLE IF EXISTS `tblsections`;
CREATE TABLE IF NOT EXISTS `tblsections` (
  `sectionID` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `section_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`sectionID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblsections`
--

INSERT INTO `tblsections` (`sectionID`, `section`, `section_code`) VALUES
(1, 'Technical Services Division', '074-02'),
(2, 'Finance & Administrative Services', '074-03'),
(3, 'Office of the Regional Director', '074-01'),
(4, 'USTC', '074-10'),
(5, 'PSTC-MT. PROVINCE', '074-09'),
(6, 'PSTC-ABRA', '074-04'),
(7, 'PSTC-APAYAO', '074-05'),
(8, 'PSTC-BENGUET', '074-06'),
(9, 'PSTC-IFUGAO', '074-07'),
(10, 'PSTC-KALINGA', '074-08');

-- --------------------------------------------------------

--
-- Table structure for table `tblsignatories`
--

DROP TABLE IF EXISTS `tblsignatories`;
CREATE TABLE IF NOT EXISTS `tblsignatories` (
  `signatoryID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `position` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `signType` set('approval','chairman','vice-chairman','member') COLLATE utf8_unicode_ci DEFAULT 'member',
  `absOrder` int(11) NOT NULL DEFAULT '0',
  `active` set('yes','no') COLLATE utf8_unicode_ci DEFAULT 'yes',
  `p_req` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `rfq` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `abs` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `ors` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `iar` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `dv` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `ris` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `par` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  `ics` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y',
  PRIMARY KEY (`signatoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbltemp_inventory_supply`
--

DROP TABLE IF EXISTS `tbltemp_inventory_supply`;
CREATE TABLE IF NOT EXISTS `tbltemp_inventory_supply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `documentNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `itemNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unitIssue` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unitValue` float(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int(5) NOT NULL DEFAULT '0',
  `onHandCount` int(5) NOT NULL DEFAULT '0',
  `quantityShortage` int(5) NOT NULL DEFAULT '0',
  `valueShortage` float(10,2) NOT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbltemp_procurement_monitoring`
--

DROP TABLE IF EXISTS `tbltemp_procurement_monitoring`;
CREATE TABLE IF NOT EXISTS `tbltemp_procurement_monitoring` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moYear` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prNo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abstractApprovalDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poApprovalDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `supplier` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `particulars` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poRecievedDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deliveredDate` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoiceNo` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inspectedBy` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `requiredDays` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `actualDays` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `difference` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbltransferred`
--

DROP TABLE IF EXISTS `tbltransferred`;
CREATE TABLE IF NOT EXISTS `tbltransferred` (
  `invID` int(11) NOT NULL DEFAULT '0',
  `item_from` int(11) NOT NULL,
  `item_to` int(11) NOT NULL,
  `date_received` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` longblob NOT NULL,
  PRIMARY KEY (`invID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblunit_issue`
--

DROP TABLE IF EXISTS `tblunit_issue`;
CREATE TABLE IF NOT EXISTS `tblunit_issue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unitName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblunit_issue`
--

INSERT INTO `tblunit_issue` (`id`, `unitName`) VALUES
(1, 'Bag'),
(2, 'Bar'),
(3, 'Book'),
(4, 'Box'),
(5, 'Bundle'),
(6, 'Can'),
(7, 'Cartoon'),
(8, 'J.O.'),
(9, 'Kilo'),
(10, 'Pack'),
(11, 'Pad'),
(12, 'Pair'),
(13, 'Piece'),
(14, 'Ream'),
(15, 'Roll'),
(16, 'Set'),
(17, 'Tube'),
(18, 'Unit'),
(19, 'lot'),
(20, 'Bottle'),
(21, 'gallon'),
(22, 'liter'),
(23, 'meter'),
(24, 'quart'),
(25, 'pint'),
(26, 'yard'),
(27, 'Kg'),
(28, 'Cu.m.'),
(29, 'pax'),
(30, 'Meal'),
(31, 'Way'),
(32, 'Night'),
(33, 'vial'),
(34, 'tank'),
(35, 'packet'),
(36, 'Snacks'),
(37, 'month'),
(38, 'Pouch'),
(39, 'bd/ft'),
(40, 'Sq. ft.'),
(41, 'g');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
