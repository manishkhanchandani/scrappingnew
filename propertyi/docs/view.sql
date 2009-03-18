-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 18, 2009 at 02:24 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `scrappingnew`
-- 

-- --------------------------------------------------------

-- 
-- Stand-in structure for view `ip_nofound`
-- 
CREATE TABLE `ip_nofound` (
`cnt` bigint(21)
,`country` varchar(10)
);
-- --------------------------------------------------------

-- 
-- Stand-in structure for view `ip_noreview`
-- 
CREATE TABLE `ip_noreview` (
`cnt` bigint(21)
,`country` varchar(10)
);
-- --------------------------------------------------------

-- 
-- Stand-in structure for view `ip_review`
-- 
CREATE TABLE `ip_review` (
`cnt` bigint(21)
,`country` varchar(10)
);
-- --------------------------------------------------------

-- 
-- Stand-in structure for view `ip_total`
-- 
CREATE TABLE `ip_total` (
`cnt` bigint(21)
,`country` varchar(10)
);
-- --------------------------------------------------------

-- 
-- Structure for view `ip_nofound`
-- 
DROP TABLE IF EXISTS `ip_nofound`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `scrappingnew`.`ip_nofound` AS select count(`scrappingnew`.`property_xml`.`country`) AS `cnt`,`scrappingnew`.`property_xml`.`country` AS `country` from `scrappingnew`.`property_xml` where ((`scrappingnew`.`property_xml`.`hotel_id` <> 0) and (`scrappingnew`.`property_xml`.`gotit` = 0) and (`scrappingnew`.`property_xml`.`flag` = 1)) group by `scrappingnew`.`property_xml`.`country`;

-- --------------------------------------------------------

-- 
-- Structure for view `ip_noreview`
-- 
DROP TABLE IF EXISTS `ip_noreview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `scrappingnew`.`ip_noreview` AS select count(`scrappingnew`.`property_xml`.`country`) AS `cnt`,`scrappingnew`.`property_xml`.`country` AS `country` from `scrappingnew`.`property_xml` where ((`scrappingnew`.`property_xml`.`hotel_id` <> 0) and (`scrappingnew`.`property_xml`.`gotit` = 1) and (`scrappingnew`.`property_xml`.`totalreview` < 1) and (`scrappingnew`.`property_xml`.`flag` = 1)) group by `scrappingnew`.`property_xml`.`country`;

-- --------------------------------------------------------

-- 
-- Structure for view `ip_review`
-- 
DROP TABLE IF EXISTS `ip_review`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `scrappingnew`.`ip_review` AS select count(`scrappingnew`.`property_xml`.`country`) AS `cnt`,`scrappingnew`.`property_xml`.`country` AS `country` from `scrappingnew`.`property_xml` where ((`scrappingnew`.`property_xml`.`hotel_id` <> 0) and (`scrappingnew`.`property_xml`.`gotit` = 1) and (`scrappingnew`.`property_xml`.`totalreview` > 0) and (`scrappingnew`.`property_xml`.`flag` = 1)) group by `scrappingnew`.`property_xml`.`country`;

-- --------------------------------------------------------

-- 
-- Structure for view `ip_total`
-- 
DROP TABLE IF EXISTS `ip_total`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `scrappingnew`.`ip_total` AS select count(`scrappingnew`.`property_xml`.`country`) AS `cnt`,`scrappingnew`.`property_xml`.`country` AS `country` from `scrappingnew`.`property_xml` where ((`scrappingnew`.`property_xml`.`hotel_id` <> 0) and (`scrappingnew`.`property_xml`.`flag` = 1)) group by `scrappingnew`.`property_xml`.`country`;
