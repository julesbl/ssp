<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:   Simple Site Protection
*   Routine:	adminusercreation.php
*   Created:	07/04/2005
*   Descrip:	Simple user creation for site administrator.
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
*   Rev. Date	07/04/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	08/09/2007
*   Descrip:	Large changes to the admin class.
*
*   Revision:	c
*   Rev. Date	14/01/2016
*   Descrip:	Composer implemented.
*/
namespace w34u\ssp;

require 'includeheader.php';

$session = new Protect("admin");

$ssp = new Setup($session, true);

$admin = new UserAdmin($session, $ssp, "", $session->userId);

$userId = $admin->userCreate(true);
if($userId === true){
	$tpl = $ssp->tpl(array('title' => 'User created', 'content' => '<h1>New user created</h1>'));
	echo $tpl->output();
}
else{
	echo $userId;
}


?>