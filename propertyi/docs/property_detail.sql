-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 18, 2009 at 02:35 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `scrappingnew`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `property_detail`
-- 

CREATE TABLE `property_detail` (
  `id` int(11) NOT NULL auto_increment,
  `poi_id` int(11) NOT NULL,
  `poi_name` varchar(255) default NULL,
  `reviewer` varchar(255) default NULL,
  `reviewdate` varchar(50) default NULL,
  `review_title` varchar(100) default NULL,
  `rating` varchar(20) default NULL,
  `review_detail` text,
  `source` text,
  `filename` text,
  `targetSite` varchar(255) default NULL,
  `avgrating` varchar(20) default NULL,
  `xml_id` int(11) default NULL,
  `country` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;
