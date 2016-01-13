<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:   Simple Site Protection
*   Routine:	adminusercreation.php
*   Created:	07/04/2005
*   Descrip:	Simple user creation for site administrator.
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
*   Rev. Date	07/04/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	08/09/2007
*   Descrip:	Large changes to the admin class.
*/
require("includeheader.php");
require($SSP_IncludePath."htmlobjects.php");
$session = new Protect("admin");

$ssp = new Setup($session, true);

$admin = new SSP_UserAdmin($session, $ssp, "", $session->userId);

$userId = $admin->userCreate(true);
if($userId === true){
	$tpl = $ssp->tpl(array('title' => 'User created', 'content' => '<h1>New user created</h1>'));
	echo $tpl->output();
}
else{
	echo $userId;
}


?>