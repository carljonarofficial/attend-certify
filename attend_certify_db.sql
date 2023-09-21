-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 23, 2022 at 03:28 AM
-- Server version: 10.5.13-MariaDB-cll-lve
-- PHP Version: 7.2.34

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u426099866_db_attnd_crtfy`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `ID` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`ID`, `username`, `password`, `email`, `status`) VALUES
(1, 'carljonar', '$2y$10$N5Q0OCnzsqgnBBbIMZIk7.AFnsArw6DcA8QkOhJgxONWxSgbfIUP2', 'cjpalado@gmail.com', 1),
(2, 'stevebaludda', '$2y$10$cMyI0R5ujuQHNNU.Hp3YEuUQtg7z2SjJEiuh/.V/Zxne7d7skIhWu', 'sdbaludda.ccit@unp.edu.ph', 1),
(3, 'joponce', '$2y$10$SsiEl3M58uWEJjtPzDPeteBW83J3jyjLgU6q7lTx7D/5P54YUkbka', 'jodponce.ccit@unp.edu.ph', 1),
(4, 'clarenceengr', '$2y$10$kiDkrl7O11uF9gU3yrgA6u4MX10ZEwVT/hqzisT.b28cfoAgqftt.', 'cdpfernandez.ccit@unp.edu.ph', 1),
(5, 'paulrabino', '$2y$10$HCVEri7Wu1yOkIErUKBcIe13QgRFu1vGvMwGHvQOIO9PzdSIwndNy', 'paarabino.ccit@unp.edu.ph', 1),
(6, 'jeffbaldovino', '$2y$10$1mFGo3/t4KMT2..gvkFCruLA.1G15KZoTd90GXRzS5qLEe7/.eu6K', 'jmcbaldovino.ccit@unp.edu.ph', 1),
(7, 'jezordonez', '$2y$10$mslriWF91WZM5pzrxZrFt.f7gsNVWQqovgy2sWU/UBAFIj7cQhGc.', 'bjcordonez.ccit@unp.edu.ph', 1),
(8, 'cjnpalado.ccit20220107160918', '$2y$10$oaOsKSyM/Rz0bXTF0MJtZ.4Mv0tV/5UofNs94ch/GcVBM.aQAjmpW', 'cjnpalado.ccit@unp.edu.ph', 1),
(9, 'paknerb20220119083838', '$2y$10$BBjtnKeL83rsENg28qKzVOhMb2DjlCsRRug86Gy2zwMsqMmfaW1ka', 'paknerb@gmail.com', 1),
(10, 'smavillarin.ccit20220123025043', '$2y$10$mJPcbFaU1ycQ1HZ793wtW.oKrHcB7fPLIKGdzSak2Rzz1EL7.Y2oa', 'smavillarin.ccit@unp.edu.ph', 1),
(11, 'ItsMeMotherFucker', '$2y$10$gF.xuje71G9S2efD/p.Lt.l5ErrnNHacBmgDx5UlQHunwrVqa3rXe', 'yapape1333@diolang.com', 1),
(12, 'sample', '$2y$10$esa2z6q3x8TR6lTkBPzmm.KaUrFXiRFrB2F80uPbcm82dWWEabZNu', 'sample@gmail.com', 1),
(13, 'darylbuen', '$2y$10$fC4eJr/4wbtouI8nmqhoEOPKy.DJl8heH7inLvZrWGgrDeTJqa.FK', 'darylsubmissions@gmail.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `ID` int(11) NOT NULL,
  `event_ID` int(11) NOT NULL,
  `invitee_code` text NOT NULL,
  `datetime_attendance` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`ID`, `event_ID`, `invitee_code`, `datetime_attendance`, `status`) VALUES
(1, 1, 'IVT-20210713191236-E4FIKC', '2021-08-13 23:40:53', 1),
(2, 1, 'IVT-20210713190505-MKXSPT', '2021-08-13 23:42:17', 1),
(3, 2, 'IVT-20210713191505-MEQWK7', '2021-08-13 23:47:50', 1),
(4, 2, 'IVT-20210713191806-B6831F', '2021-08-13 23:49:14', 1),
(5, 4, 'IVT-20211008004556-OER83V', '2021-10-08 00:48:50', 1),
(6, 6, 'IVT-20211018003100-XGDVWO', '2021-11-03 11:43:18', 1),
(7, 2, 'IVT-20210804221540-JML4HO', '2022-01-20 04:51:35', 1),
(8, 7, 'IVT-20220117222223-VTCZEB', '2022-03-20 13:25:26', 1),
(9, 11, 'IVT-20220321103340-SF65KN', '2022-03-21 02:49:11', 1),
(10, 14, 'IVT-20220403222150-GOVKE4', '2022-04-03 16:42:32', 1),
(11, 15, 'IVT-20220405230844-EJR69T', '2022-04-06 02:14:04', 1),
(12, 15, 'IVT-20220405230844-EJR69T', '2022-04-05 18:38:46', 1),
(13, 15, 'IVT-20220405230844-EJR69T', '2022-04-06 22:39:33', 1),
(14, 15, 'IVT-20220405230844-EJR69T', '2022-04-07 07:43:53', 1),
(15, 2, 'IVT-20210804221540-JML4HO', '2022-04-09 22:19:23', 1),
(16, 2, 'IVT-20210804221540-JML4HO', '2022-04-19 10:26:41', 1),
(17, 19, 'IVT-20220425203457-KWVYJB', '2022-04-25 20:38:23', 1),
(18, 19, 'IVT-20220425203457-KWVYJB', '2022-04-26 10:44:57', 1),
(19, 19, 'IVT-20220425203457-KWVYJB', '2022-04-28 06:55:54', 1),
(20, 19, 'IVT-20220425203457-KWVYJB', '2022-05-02 21:37:59', 1),
(21, 19, 'IVT-20220425203457-KWVYJB', '2022-05-03 22:49:41', 1),
(22, 19, 'IVT-20220425203457-KWVYJB', '2022-05-04 09:40:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `certificate`
--

CREATE TABLE `certificate` (
  `ID` int(11) NOT NULL,
  `event_ID` int(11) NOT NULL,
  `invitee_code` text NOT NULL,
  `certificate_code` text NOT NULL,
  `datetime_generated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `certificate`
--

INSERT INTO `certificate` (`ID`, `event_ID`, `invitee_code`, `certificate_code`, `datetime_generated`, `status`) VALUES
(1, 1, 'IVT-20210713191236-E4FIKC', '20210813174053-CERT-4QZ8RL', '2021-08-13 23:40:53', 1),
(2, 1, 'IVT-20210713190505-MKXSPT', '20210813174217-CERT-KONYRX', '2021-08-13 23:42:17', 1),
(3, 2, 'IVT-20210713191505-MEQWK7', '20210813174750-CERT-1EHDVR', '2021-08-13 23:47:50', 1),
(4, 2, 'IVT-20210713191806-B6831F', '20210813174914-CERT-LQOPIT', '2021-08-13 23:49:14', 1),
(5, 4, 'IVT-20211008004556-OER83V', '20211007184850-CERT-BH4QA7', '2021-10-08 00:48:50', 1),
(6, 2, 'IVT-20210804221540-JML4HO', '20220120045135-CERT-WCRX8V', '2022-01-20 04:51:35', 1),
(7, 7, 'IVT-20220117222223-VTCZEB', '20220320132526-CERT-5Y7SFQ', '2022-03-20 13:25:26', 1),
(8, 11, 'IVT-20220321103340-SF65KN', '20220321024911-CERT-ROGPTA', '2022-03-21 02:49:11', 1),
(9, 14, 'IVT-20220403222150-GOVKE4', '20220403164232-CERT-4267DC', '2022-04-03 16:42:32', 1),
(10, 15, 'IVT-20220405230844-EJR69T', '20220405170222-CERT-EK5T4C', '2022-04-05 17:02:22', 1),
(13, 19, 'IVT-20220425203457-KWVYJB', '20220425203823-CERT-6IVND2', '2022-04-25 12:38:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `certificate_config`
--

CREATE TABLE `certificate_config` (
  `ID` int(11) NOT NULL,
  `event_ID` int(11) NOT NULL,
  `page_layout` varchar(10) NOT NULL,
  `text_style` varchar(20) NOT NULL,
  `text_color` varchar(12) NOT NULL,
  `text_position` varchar(10) NOT NULL,
  `barcode_position` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `certificate_config`
--

INSERT INTO `certificate_config` (`ID`, `event_ID`, `page_layout`, `text_style`, `text_color`, `text_position`, `barcode_position`) VALUES
(1, 1, 'L-Letter', 'Helvetica-B-30', '#e22222', '130,79', '20,169'),
(2, 2, 'L-Letter', 'Helvetica-B-40', '#021436', '130,90', '20,174'),
(3, 3, 'L-A4', 'Helvetica-B-40', '#f2663a', '140,79', '20,180'),
(4, 4, 'L-A4', 'Helvetica--40', '#000000', '140,85', '20,180'),
(5, 5, 'L-A4', 'Helvetica-B-40', '#000000', '145,85', '20,169'),
(6, 6, 'L-A4', 'Helvetica-B-30', '#866a1d', '140,83', '20,169'),
(7, 7, 'L-A4', 'Helvetica-B-35', '#000000', '138,87', '20,169'),
(8, 11, 'L-A4', 'Times-B-30', '#f1095a', '140,79', '20,169');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `ID` int(11) NOT NULL,
  `admin_ID` int(11) NOT NULL,
  `event_title` varchar(500) NOT NULL,
  `date` date NOT NULL,
  `date_end` date DEFAULT NULL,
  `time_inclusive` time NOT NULL,
  `time_conclusive` time NOT NULL,
  `venue` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `agenda` text NOT NULL,
  `theme` varchar(500) NOT NULL,
  `certificate_template` varchar(250) NOT NULL,
  `datetime_added` datetime NOT NULL DEFAULT current_timestamp(),
  `datetime_edited` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`ID`, `admin_ID`, `event_title`, `date`, `date_end`, `time_inclusive`, `time_conclusive`, `venue`, `description`, `agenda`, `theme`, `certificate_template`, `datetime_added`, `datetime_edited`, `status`) VALUES
(1, 1, 'Independence Day', '2022-01-26', '2022-01-27', '08:00:00', '10:30:00', 'Rizal Park', 'Independence Day is an annual national holiday in the Philippines observed on June 12.', 'The Independence Day festivities will take a different turn this year.', 'Celebrate the freedom', 'Cert-template-1-2021-10-29-19-49-14-000000.pdf', '2021-05-30 01:00:18', '2022-04-18 21:49:23', 1),
(2, 1, 'Ayuda Distribution 2022', '2022-04-19', '2022-04-21', '08:00:00', '17:00:00', 'Vigan Convention Center', 'This event is for the residents affected by COVID-19 Pandemic in providing assistance thereof.', 'Distribute assistance (ayuda)', 'We Help as One', 'Cert-template-1-2021-08-14-00-32-45-000000.pdf', '2021-05-31 07:09:44', '2022-04-19 10:22:20', 1),
(3, 2, 'Social Media Marketing Seminar', '2021-11-12', '2021-11-12', '08:30:00', '12:00:00', 'UNP-CCIT', 'The seminar refers to the use of social media and social networks to market a company’s products and services.', 'To inform attendees on the importance of social media marketing.', 'The way we connect with one another.', 'Cert-template-2-2021-09-28-20-57-37-000000.pdf', '2021-09-28 20:57:37', NULL, 1),
(4, 1, 'Social Seminar on Tiktok', '2021-12-30', '2022-01-04', '08:00:00', '10:30:00', 'Vigan Convention Center', 'Current Trends on Tiktok', 'Familiarize these Trends', 'Create and Share', 'Cert-template-1-2021-10-17-21-32-19-000000.pdf', '2021-10-05 19:09:49', NULL, 1),
(5, 2, 'Mental Health Awarness Seminar', '2021-12-01', '2021-12-01', '09:00:00', '11:30:00', 'Hotel Linda Suites', 'Mental health includes our emotional, psychological, and social well-being.', 'Discuss recent issues surrounding mental health.', 'Mental Health Keep Healthy Mind', 'Cert-template-2-2021-10-08-01-09-48-000000.pdf', '2021-10-08 01:09:48', NULL, 1),
(6, 1, 'Holiday Season Seminar and Training', '2021-12-06', '2021-12-06', '10:00:00', '14:00:00', 'Safari Hotel and Resort', 'Holidays are essential to our lives that makes us happy and refreshing.', 'To keep update on the trends of holidays', 'Wonderful and Peaceful Holidays', 'Cert-template-1-2021-10-17-22-25-49-000000.pdf', '2021-10-17 22:24:57', NULL, 1),
(7, 1, 'Final Defense', '2022-04-09', '2022-04-09', '09:00:00', '09:30:00', 'UNP-CCIT', 'This is a final requirement for graduation.', 'To pass and defend this subject.', 'Defend', 'Cert-template-1-2021-10-26-11-19-15-000000.pdf', '2021-10-26 11:19:15', NULL, 1),
(8, 1, 'Career Placement Seminar', '2022-03-12', '2022-03-12', '08:00:00', '09:00:00', 'UNP-Gymnasium', 'This seminar intended for graduating students who will seek their jobs.', 'To gain their knowledge for seeking their jobs.', 'To seek and gain', 'Cert-template-1-2022-01-19-16-38-49-000000.pdf', '2022-01-19 08:38:49', NULL, 1),
(9, 11, 'Tara ChongKi', '2022-02-10', '2022-02-10', '00:33:00', '00:34:00', 'Max\'x', 'Happybirthday', 'Agenda LUlipap', 'New Houses', 'Cert-template-11-2022-02-13-22-29-26-000000.pdf', '2022-02-13 14:29:26', NULL, 0),
(10, 11, 'Intro to Programming', '2022-02-13', '2022-02-13', '05:00:00', '05:01:00', 'CCIT', 'Example Description', 'None', 'Programming', 'Cert-template-11-2022-02-13-22-36-18-000000.pdf', '2022-02-13 14:36:18', NULL, 1),
(11, 3, 'FINAL DEFENSE', '2022-03-21', '2022-03-21', '09:00:00', '09:30:00', 'CCIT', 'Presentation of Capstone Project', 'Defend the Capstone Project', 'Defense 2022', 'Cert-template-3-2022-03-21-10-32-08-000000.pdf', '2022-03-21 02:32:08', NULL, 1),
(12, 12, 'aa', '2022-03-21', '2022-03-21', '09:00:00', '09:30:00', '..', '..', '..', '..', 'Cert-template-12-2022-03-21-10-50-04-000000.pdf', '2022-03-21 02:50:04', NULL, 1),
(13, 3, 'RALLY', '2022-03-22', '2022-03-22', '08:00:00', '10:00:00', 'CCIT', 'ASDSA', 'TITLE DEFENSE', 'SADQ', 'Cert-template-3-2022-03-21-11-00-29-000000.pdf', '2022-03-21 03:00:29', NULL, 1),
(14, 1, 'Test Event', '2022-04-03', '2022-04-03', '00:00:00', '01:00:00', 'Tamag Grounds', 'To test this event', 'None', 'None', 'Cert-template-1-2022-04-03-15-15-38-000000.pdf', '2022-04-03 07:15:38', NULL, 1),
(15, 1, 'Test', '2022-04-04', '2022-04-06', '00:00:00', '01:00:00', 'Test', 'Test', 'Test', 'Test', 'Cert-template-1-2022-04-03-16-20-09-000000.pdf', '2022-04-03 08:20:09', NULL, 1),
(16, 1, 'Test Event 3', '2022-04-09', '2022-04-10', '00:00:00', '01:00:00', 'Test Event 3', 'Test Event 3', 'Test Event 3', 'Test Event 3', 'Cert-template-1-2022-04-03-16-33-16-000000.pdf', '2022-04-03 16:33:16', NULL, 1),
(17, 1, 'Graduation Seminar', '2022-05-01', '2022-05-05', '00:00:00', '01:00:00', 'Test', 'Test', 'Test', 'Test', 'Cert-template-1-2022-04-05-22-28-12-000000.pdf', '2022-04-05 22:28:12', NULL, 1),
(18, 1, 'Graduation', '2022-04-30', '2022-04-30', '00:00:00', '01:00:00', 'CCIT', 'na', 'na', 'na', 'Cert-template-1-2022-04-19-10-19-56-000000.pdf', '2022-04-19 10:19:56', '2022-04-19 10:21:02', 1),
(19, 1, 'Career Placement', '2022-05-02', '2022-05-06', '05:00:00', '21:00:00', 'Tadena Hall', 'Na', 'Sample', 'Sample', 'Cert-template-1-2022-04-19-10-23-30-000000.pdf', '2022-04-19 10:23:30', '2022-05-02 21:36:45', 1);

-- --------------------------------------------------------

--
-- Table structure for table `invitees`
--

CREATE TABLE `invitees` (
  `ID` int(11) NOT NULL,
  `event_ID` int(11) NOT NULL,
  `invitee_code` text NOT NULL,
  `firstname` varchar(500) NOT NULL,
  `middlename` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phonenum` varchar(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `datetime_added` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invitees`
--

INSERT INTO `invitees` (`ID`, `event_ID`, `invitee_code`, `firstname`, `middlename`, `lastname`, `email`, `phonenum`, `type`, `datetime_added`, `status`) VALUES
(1, 1, 'IVT-20210713190505-MKXSPT', 'Steven', 'Darang', 'Baludda', 'sdbaludda.ccit@unp.edu.ph', '09789094567', 'Student', '2021-07-13 19:05:05', 1),
(2, 1, 'IVT-20210713191147-LFS17V', 'John Oliver', 'Dela Cruz', 'Ponce', 'jodponce.ccit@unp.edu.ph', '09796780989', 'Guest', '2021-07-13 19:11:47', 1),
(3, 1, 'IVT-20210713191236-E4FIKC', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Employee', '2021-07-13 19:12:36', 1),
(4, 2, 'IVT-20210713191505-MEQWK7', 'Daryl', 'Villanueva', 'Buen', 'darylsubmissions@gmail.com', '09786453678', 'Guest', '2021-07-13 19:15:05', 1),
(5, 2, 'IVT-20210713191806-B6831F', 'Steven', 'Darang', 'Baludda', 'stevenbaludda@gmail.com', '09368411428', 'Student', '2021-07-13 19:18:06', 1),
(6, 2, 'IVT-20210804221540-JML4HO', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Employee', '2021-08-04 22:15:40', 1),
(7, 1, 'IVT-20210824103953-NO3V0M', 'John Laylord', 'Castillo', 'Ronque', 'jlcronque.ccit@unp.edu.ph', '09456789099', 'Guest', '2021-08-24 10:39:53', 0),
(8, 4, 'IVT-20211008004556-OER83V', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Student', '2021-10-08 00:45:56', 1),
(9, 4, 'IVT-20211017232406-A2V37K', 'Steven', 'Darang', 'Baludda', 'baluddasteven@gmail.com', '09789800989', 'Guest', '2021-10-17 23:24:06', 1),
(10, 6, 'IVT-20211018003100-XGDVWO', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Guest', '2021-10-18 00:31:00', 1),
(11, 7, 'IVT-20220117222223-VTCZEB', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Student', '2022-01-17 22:22:23', 1),
(12, 8, 'IVT-20220119194149-GNV62W', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Student', '2022-01-19 11:41:49', 1),
(13, 10, 'IVT-20220213223812-D0SMKC', 'Abc', 'Def', 'Ghi', 'example123@gmail.com', '09090920121', 'Student', '2022-02-13 14:38:12', 1),
(14, 7, 'IVT-20220215023734-542W7Y', 'Steven', 'Darang', 'Baludda', 'cjpalado@gmail.com', '09567890989', 'Guest', '2022-02-14 18:37:34', 1),
(15, 11, 'IVT-20220321103340-SF65KN', 'John', 'de LA cruz', 'Ponce', 'jodponce.ccit@unp.edu.ph', '09123456789', 'Student', '2022-03-21 02:33:40', 1),
(16, 11, 'IVT-20220321103719-AE1RY2', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Employee', '2022-03-21 02:37:19', 1),
(17, 12, 'IVT-20220321111950-XSGMAJ', 'vk', 'sison', 'toyang', 'vksison@unp.edu.ph', '69584758412', 'Employee', '2022-03-21 03:19:50', 1),
(18, 11, 'IVT-20220321113803-UB3R5O', 'Richard', 'Corpuz', 'Arruejo', 'rcarruejo0605@gmail.com', '09975818628', 'Employee', '2022-03-21 03:38:03', 1),
(19, 14, 'IVT-20220403222150-GOVKE4', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Employee', '2022-04-03 22:21:50', 1),
(20, 15, 'IVT-20220405230844-EJR69T', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Student', '2022-04-05 23:08:44', 1),
(21, 16, 'IVT-20220409132354-623M9S', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Student', '2022-04-09 13:23:54', 1),
(22, 18, 'IVT-20220419125731-H87FIX', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Employee', '2022-04-19 12:57:31', 1),
(23, 19, 'IVT-20220425203457-KWVYJB', 'Carl Jonar', 'Navarro', 'Palado', 'cjpalado@gmail.com', '09368411428', 'Faculty', '2022-04-25 20:34:57', 1);

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `ID` int(11) NOT NULL,
  `event_ID` int(11) NOT NULL,
  `openRegistration` tinyint(4) NOT NULL DEFAULT 0,
  `allowedEmp` tinyint(4) NOT NULL DEFAULT 0,
  `allowedStud` tinyint(4) NOT NULL DEFAULT 0,
  `allowedFaculty` tinyint(4) NOT NULL DEFAULT 0,
  `allowedGuest` tinyint(4) NOT NULL DEFAULT 0,
  `allowedAll` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`ID`, `event_ID`, `openRegistration`, `allowedEmp`, `allowedStud`, `allowedFaculty`, `allowedGuest`, `allowedAll`) VALUES
(1, 7, 1, 0, 1, 1, 0, 0),
(2, 11, 1, 1, 0, 0, 0, 0),
(3, 12, 1, 1, 1, 0, 0, 0),
(4, 14, 1, 1, 1, 0, 0, 0),
(5, 15, 1, 0, 0, 0, 1, 0),
(6, 16, 1, 1, 0, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_attendance_events` (`event_ID`);

--
-- Indexes for table `certificate`
--
ALTER TABLE `certificate`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_certificate_events` (`event_ID`);

--
-- Indexes for table `certificate_config`
--
ALTER TABLE `certificate_config`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_certificate_config_events` (`event_ID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_events_admin_accounts` (`admin_ID`);

--
-- Indexes for table `invitees`
--
ALTER TABLE `invitees`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_invitees_events` (`event_ID`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `event_ID` (`event_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `certificate`
--
ALTER TABLE `certificate`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `certificate_config`
--
ALTER TABLE `certificate_config`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `invitees`
--
ALTER TABLE `invitees`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `FK_attendance_events` FOREIGN KEY (`event_ID`) REFERENCES `events` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `certificate`
--
ALTER TABLE `certificate`
  ADD CONSTRAINT `FK_certificate_events` FOREIGN KEY (`event_ID`) REFERENCES `events` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `certificate_config`
--
ALTER TABLE `certificate_config`
  ADD CONSTRAINT `FK_certificate_config_events` FOREIGN KEY (`event_ID`) REFERENCES `events` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `FK_events_admin_accounts` FOREIGN KEY (`admin_ID`) REFERENCES `admin_accounts` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `invitees`
--
ALTER TABLE `invitees`
  ADD CONSTRAINT `FK_invitees_events` FOREIGN KEY (`event_ID`) REFERENCES `events` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`event_ID`) REFERENCES `events` (`ID`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
