-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 23, 2009 at 11:47 AM
-- Server version: 5.0.45
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `powdemo`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `restaurants`
-- 

CREATE TABLE `restaurants` (
  `restaurant_id` int(11) NOT NULL auto_increment,
  `id` int(11) NOT NULL default '0',
  `rid` varchar(255) default NULL,
  `state` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `address` text,
  `neighborhood` varchar(255) default NULL,
  `country` varchar(50) default NULL,
  `location` varchar(255) default NULL,
  `phone` varchar(200) default NULL,
  `zip` varchar(50) default NULL,
  `linktext` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `full_neighborhood` varchar(255) default NULL,
  `full_city` varchar(255) default NULL,
  `pricerange` varchar(255) default NULL,
  `dollarcount` int(11) default NULL,
  `cusine` text,
  PRIMARY KEY  (`restaurant_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
