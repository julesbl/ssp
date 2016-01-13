<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    SSP Emergency admin create
*   Routine:	emergencyadmincreate.php
*   Created:	30/11/2005
*   Descrip:	Create an admin user if there is non in the database.
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
*   Rev. Date	30/11/2005
*   Descrip:	Created.
*/
require("../includeheader.php");

	$session = new Protect();
	$ssp = new Setup($session);
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
<title>SSP Emergency admin create</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
	<h2>SSP Emergency admin succesfully created</h2>
	<p>Admin User creation succesfull, Username: admin, email: admin@admin.com, password: password1000. Change all these details immediately.</p>
	<p><a href="<?php echo $SSP_Config->adminDir; ?>">Go to admin</a></p>
</body>
</html>