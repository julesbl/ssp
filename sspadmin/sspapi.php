<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	sspapi.php
*   Created:	10/04/2009
*   Descrip:	Api to remote site (currently not working).
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
*   Rev. Date	10/04/2009
*   Descrip:	Created.
*
*/
require("includeheader.php");
require($SSP_IncludePath. "API_interface.php");

$session = new SSP_Protect();

$api = new API_interface($SSP_Config->apiEncryptionKey, $SSP_Config->remoteSystemApi, "");
$api->mirror = $SSP_Config->mirror;
$api->setupSspInterface($session, $SSP_DB, $SSP_Config->tableAutoLogin, $_SERVER['SERVER_ADDR']);
$api->parseMessage();
?>