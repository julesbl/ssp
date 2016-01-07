<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	include.php
*   Created:	07/01/2005
*   Descrip:	Includes libraries and routines needed.
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
*   Rev. Date	07/01/2005
*   Descrip:	Created.
*/

// configure PHP error handling
if($devServer){
	error_reporting(E_ALL ^ E_STRICT);
}

// un-comment the database libraries you wish to use and sort out the
// configuration config.php accordingly

// include pear library routines
//require_once("PEAR.php");
//require_once("DB.php");
//require($SSP_IncludePath."db_pear.php");

// include adodb library routines
require_once($SSP_IncludePath. "adodb/adodb.inc.php");
require($SSP_IncludePath. "db_adodb.php");

// include MySql DB libraries if used
// require($SSP_IncludePath."db_mysql.php");

// data type checking library
require($SSP_IncludePath. "SSP_dataCheck.php");

// form creation library
require($SSP_IncludePath."formclasses.php");

// base functions
require($SSP_IncludePath."functions.php");

// base classes
require($SSP_IncludePath."SSP_ProtectBase.php");
require($SSP_IncludePath."SSP_LogonBase.php");

// base classes
require($SSP_IncludePath."SSP_email.php");

// user admin classes
require($SSP_IncludePath."adminclasses.php");

// user config classes
require($SSP_IncludePath."userclasses.php");

// template classes
require($SSP_IncludePath."templateclasses.php");

// main project and template setup
require($SSP_IncludePath."SSP_setup.php");

// application configuration
require($SSP_IncludePath."config.php");

// gloabal variable, contant and php setup
require($SSP_IncludePath."setup.php");

// session routines and configuration
require($SSP_IncludePath."session.php");


/* End of file include.php */
/* Location: ./sspincludes/include.php */