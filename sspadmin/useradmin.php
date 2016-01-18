<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	useradmin.php
*   Created:	12/02/2005
*   Descrip:	Administer a particular user.
*
*   Copyright 2005-2016 Julian Blundell, w34u
*
*   This file is part of Simple Site Protection (SSP).
*
*   SSP is free software; you can redistribute it and/or modify
*   it under the terms of the The MIT License (MIT)
*   as published by the Open Source Initiative.
*
*   SSP is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   The MIT License (MIT) for more details.
*
*   Revision:	a
*   Rev. Date	12/02/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	28/06/2011
*   Descrip:	Improved structure and template handling.
*
*   Revision:	c
*   Rev. Date	18/01/2016
*   Descrip:	Composer implemented.
*/
namespace w34u\ssp;

require 'includeheader.php';

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

$admin = new UserAdmin($session, $ssp, $command, $userId);

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