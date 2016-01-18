<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	newpassword.php
*   Created:	18/07/2005
*   Descrip:	Prompts a user who has lost their password and follows the
*				link in the recovery email to enter a new password.
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
*   Rev. Date	18/07/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	18/01/2016
*   Descrip:	Composer implemented.
*/
namespace w34u\ssp;

require 'includeheader.php';

$session = new Protect("", false, false);
$ssp = new Setup($session);

$contentMain = array();
$ssp->pageTitleAdd("Password recovery, enter new password");

$admin = new UserAdmin($session, $ssp, "", "", "sspsmalltemplate.tpl", false);
if(isset($_GET['token'])){
	$token = $_GET['token'];
}
else{
	$token = false;
}
echo $admin->finishPasswordRecovery($token);
?>
