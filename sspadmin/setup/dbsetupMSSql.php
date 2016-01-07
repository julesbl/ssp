<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    SSP Database setup routine
*   Routine:	dbsetupMSSql.php
*   Created:	31/01/2008
*   Descrip:	Setup MS SQL SSP database based on config, create default admin entry.
*
*   Copyright 2005-2009 Julian Blundell, w34u
*
*   This file is part of Simple Site Protection (SSP).
*
*   SSP is free software; you can redistribute it and/or modify
*   it under the terms of the COMMON DEVELOPMENT AND DISTRIBUTION
*   LICENSE (CDDL) Version 1.0 as published by the Open Source Initiative.
*
*   SSP is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   COMMON DEVELOPMENT AND DISTRIBUTION LICENSE (CDDL) for more details.
*
*   You should have received a copy of the COMMON DEVELOPMENT AND DISTRIBUTION
*   LICENSE (CDDL) along with SSP; if not, view at
*   http://www.opensource.org; http://www.opensource.org/licenses/cddl1.php
*
*   Revision:	a
*   Rev. Date	31/01/2008
*   Descrip:	Created.
*/
require("../includeheader.php");
$SSP_DB = SSP_DB::get_connection();
$values = array();

$query = "CREATE TABLE ". $SSP_Config->sessionTable. " (
  SessionId char(32) NOT NULL default '',
  UserId char(32) NOT NULL default '',
  SessionTime int NOT NULL default '0',
  SessionName varchar(30) NOT NULL default '',
  SessionIp varchar(40) NOT NULL default '',
  SessionUserIp varchar(40) NOT NULL default '',
  SessionCheckIp smallint NOT NULL default '0',
  SessionRandom int NOT NULL default '0',
  SessionData varbinary NOT NULL,
  PRIMARY KEY  (SessionId),
)";
$SSP_DB->query($query, $values, "SSP Database configuration: Creating session table");

$query = "CREATE TABLE ". $SSP_Config->tokenTable. " (
  token char(32) NOT NULL default '',
  time int NOT NULL default '0',
  id varchar(50) NOT NULL default '',
  PRIMARY KEY  (token),
)";
$SSP_DB->query($query, $values, "SSP Database configuration: Creating token table");

$query = "CREATE TABLE ". $SSP_Config->userTable. " (
  UserId char(32) NOT NULL default '',
  UserEmail varchar(255) NOT NULL default '',
  UserName varchar(50) default NULL,
  UserPassword varchar(255) NOT NULL default '',
  UserIp varchar(30) NOT NULL default '',
  UserIpCheck smallint NOT NULL default '0',
  UserAccess varchar(20) NOT NULL default 'public',
  lang varchar(10) NOT NULL default '',
  country varchar(10) NOT NULL default '',
  UserDateLogon int NOT NULL default '0',
  UserDateLastLogon int NOT NULL default '0',
  UserDateCreated int NOT NULL default '0',
  UserDisabled smallint NOT NULL default '0',
  UserPending smallint NOT NULL default '0',
  UserAdminPending smallint NOT NULL default '0',
  CreationFinished smallint NOT NULL default '0',
  UserWaiting smallint NOT NULL default '0',
  UserInvisible smallint NOT NULL default '0',
  PRIMARY KEY  (UserId),
)";
$SSP_DB->query($query, $values, "SSP Database configuration: Creating login table");

$query = "CREATE TABLE ". $SSP_Config->userMiscTable. " (
  UserId char(32) NOT NULL default '',
  Title varchar(15) NOT NULL default '',
  FirstName varchar(20) NOT NULL default '',
  Initials varchar(5) NOT NULL default '',
  FamilyName varchar(30) NOT NULL default '',
  Address varchar(255) NOT NULL default '',
  TownCity varchar(30) NOT NULL default '',
  PostCode varchar(10) NOT NULL default '',
  County varchar(20) NOT NULL default '',
  Country varchar(5) NOT NULL default '',
  PRIMARY KEY  (UserId)
)";
$SSP_DB->query($query, $values, "SSP Database configuration: Creating user misc data table");

$query = "CREATE TABLE ". $SSP_Config->responseTable. " (
  token char(32) NOT NULL default '',
  time int NOT NULL default '0',
  UserId char(32) NOT NULL default '',
  PRIMARY KEY  (token)
)";
$SSP_DB->query($query, $values, "SSP Database configuration: Creating user misc data table");

	$session = new SSP_Protect();
	$ssp = new SSP_setup($session);
	$admin = new SSP_UserAdmin($session, $ssp);
	$admin->adminCreate();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!--
Site by w34u
http://www.w34u.com
info@w34u.com
 + 44 (0)1273 201344
 + 44 (0)7833 512221
 -->
<title>SSP database setup</title>
<meta name="Generator" content="EditPlus" />
<meta name="Author" content="w34u - Julian Blundell" />
<meta http-equiv="distribution" content="Global" />
<meta name="resource-type" content="document" />
<meta name="Description" content="Script to create and setup the SSP database" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
	<h2>SSP Database structure succesfully created</h2>
	<p>Admin User creation succesfull, Username: admin, email: admin@admin.com, password: password1000. Change all these details immediately.</p>
	<p><a href="<?php echo $SSP_Config->adminDir; ?>">Go to admin</a></p>
</body>
</html>