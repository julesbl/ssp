-- phpMyAdmin SQL Dump

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `SSP_DB`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `SSP_Session`
-- 

CREATE TABLE `SSP_Session` (
  `SessionId` varchar(32) NOT NULL default '',
  `UserId` varchar(32) NOT NULL default '',
  `SessionTime` int(11) NOT NULL default '0',
  `SessionName` varchar(30) NOT NULL default '',
  `SessionIp` varchar(35) NOT NULL default '',
  `SessionUserIp` varchar(35) NOT NULL default '',
  `SessionCheckIp` tinyint(4) NOT NULL default '0',
  `SessionRandom` int(11) NOT NULL default '0',
  `SessionData` blob NOT NULL,
  PRIMARY KEY  (`SessionId`),
  KEY `SessionTime` (`SessionTime`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `SSP_Token`
-- 

CREATE TABLE `SSP_Token` (
  `token` char(32) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  `id` varchar(50) NOT NULL,
  PRIMARY KEY  (`token`),
  KEY `time` (`time`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `SSP_UserLogon`
-- 

CREATE TABLE `SSP_UserLogon` (
  `UserId` varchar(32) NOT NULL default '',
  `UserEmail` varchar(255) default NULL,
  `UserName` varchar(50) default NULL,
  `UserPassword` varchar(100) NOT NULL default '',
  `UserIp` varchar(30) NOT NULL default '',
  `UserIpCheck` tinyint(4) NOT NULL default '0',
  `UserAccess` varchar(20) NOT NULL default 'public',
  `lang` varchar(10) NOT NULL,
  `country` varchar(10) NOT NULL,
  `UserDateLogon` int(11) NOT NULL default '0',
  `UserDateLastLogon` int(11) NOT NULL default '0',
  `UserDateCreated` int(11) NOT NULL default '0',
  `UserDisabled` tinyint(4) NOT NULL default '0',
  `UserPending` tinyint(4) NOT NULL default '0',
  `UserAdminPending` tinyint(4) NOT NULL default '0',
  `CreationFinished` tinyint(4) NOT NULL default '0',
  `UserWaiting` tinyint(4) NOT NULL default '0',
  `UserInvisible` tinyint(4) NOT NULL default '0',
  `remoteAccess` tinyint(1) NOT NULL,
  PRIMARY KEY  (`UserId`),
  UNIQUE KEY `UserName` (`UserName`),
  KEY `UserPassword` (`UserPassword`),
  KEY `UserEmail` (`UserEmail`),
  KEY `UserDisabled` (`UserDisabled`,`UserPending`,`UserAdminPending`,`CreationFinished`,`UserWaiting`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `SSP_UserMisc`
-- 

CREATE TABLE `SSP_UserMisc` (
  `UserId` varchar(32) NOT NULL default '',
  `Title` varchar(15) NOT NULL default '',
  `FirstName` varchar(20) NOT NULL default '',
  `Initials` varchar(5) NOT NULL default '',
  `FamilyName` varchar(30) NOT NULL default '',
  `Address` varchar(255) NOT NULL default '',
  `TownCity` varchar(30) NOT NULL default '',
  `PostCode` varchar(10) NOT NULL default '',
  `County` varchar(20) NOT NULL default '',
  `Country` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`UserId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `SSP_UserResponse`
-- 

CREATE TABLE `SSP_UserResponse` (
  `token` char(32) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  `UserId` char(32) NOT NULL default '',
  PRIMARY KEY  (`token`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Tokens for waiting for a user response';
