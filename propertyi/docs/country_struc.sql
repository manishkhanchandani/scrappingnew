-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 18, 2009 at 02:38 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `scrappingnew`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `country`
-- 

CREATE TABLE `country` (
  `iso2` varchar(6) default NULL,
  `name` varchar(768) default NULL,
  `capital` varchar(768) default NULL,
  `continent_code` varchar(6) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
