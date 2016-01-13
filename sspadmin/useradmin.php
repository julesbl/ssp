<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    Simple Site Protection
*   Routine:	useradmin.php
*   Created:	12/02/2005
*   Descrip:	Administer a particular user.
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
*   Rev. Date	12/02/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	28/06/2011
*   Descrip:	Improved structure and template handling.
*/

require("includeheader.php");
require($SSP_IncludePath."htmlobjects.php");
$session = new Protect("admin");

if(!isset($_SESSION["adminUserId"])){
	$_SESSION["adminUserId"] = "";
}
$userId =& $_SESSION["adminUserId"];

// get users userId
if(isset($_GET["userId"])){
    // GET supplied if allowed
    $userId = $_GET["userId"];
}
if($userId==""){
	$userId = $session->userId;
}
if(!$session->admin or $session->userId == $userId){
    // ask for password if user not admin or if admin user is editing their own account
    $needPassword = true;
}
else{
    $needPassword = false;
}

$ssp = new Setup($session, true);

$command = SSP_getParam("command", "info");


if($userId == $session->userId){
	$content["mainSection"] = "myDetails";
}

$admin = new SSP_UserAdmin($session, $ssp, $command, $userId);

switch($command){
	case "info":
		echo $admin->displayMisc();
		break;
	case "advInfo":
		echo $admin->displayAdminInfo();
		break;
	case "chPswd":
		echo $admin->changePassword("", $needPassword, true);
		break;
	case "chEmail":
		echo $admin->changeEmail($needPassword, true);
		break;
	case "chInfo":
		echo $admin->userMisc(false, true);
		break;
	case "chAdv":
		echo $admin->changeAdmin();
		break;
	case "joinEmail":
		$admin->sendJoinupEmail();
		break;
	case "email":
		$admin->emailUser($userId, $session->userId);
		break;
}
?>