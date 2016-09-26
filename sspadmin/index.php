<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	userlister.php
*   Created:	08/02/2005
*   Descrip:	Routine to list users.
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
*   Rev. Date	08/02/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	13/10/2006
*   Descrip:	Updated for new lister routines.
*
*   Revision:	c
*   Rev. Date	16/03/2007
*   Descrip:	Streamlined with new template processes and improved dessign.
*
*   Revision:	d
*   Rev. Date	14/01/2016
*   Descrip:	Composer implemented.
*/
namespace w34u\ssp;
require 'includeheader.php';

$session= new Protect("admin");

// check for command
$command = SSP_getParam("command", "list");

$ssp = new Setup($session, true);

$lister = new UserLister($ssp, $command);

if($command == "filterAdminPending"){
	// show admin pending users
	$lister->filter->displayAdminPending();
}
if($command == "filterNormal"){
	// show normal search
	$lister->filter->newSearch();
}


if($command == "filterChange"){
	// change search criteria
	$lister->displayFilterForm();
}

elseif($command=="delete"){
	// delete a user
	$userId = SSP_getParam("userId", "");
	echo $lister->deleteUser($userId);
}
else{
	// else go to lister
	echo $lister->lister();
}

?>
