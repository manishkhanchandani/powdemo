-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 23, 2009 at 11:52 AM
-- Server version: 5.0.45
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `powdemo`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `reviews`
-- 

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL auto_increment,
  `restaurant_id` int(11) default NULL,
  `id` int(11) default NULL,
  `score` varchar(200) default NULL,
  `rdate` varchar(200) default NULL,
  `url` varchar(255) default NULL,
  `desc` text,
  PRIMARY KEY  (`review_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
