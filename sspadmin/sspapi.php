<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	sspapi.php
*   Created:	10/04/2009
*   Descrip:	Api to remote site (currently not working).
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
*   Rev. Date	10/04/2009
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	18/01/2016
*   Descrip:	Composer implemented.
*/
namespace w34u\ssp;
die();
require 'includeheader.php';

$session = new Protect();

$api = new API_interface($SSP_Config->apiEncryptionKey, $SSP_Config->remoteSystemApi, "");
$api->mirror = $SSP_Config->mirror;
$api->setupSspInterface($session, $SSP_DB, $SSP_Config->tableAutoLogin, $_SERVER['SERVER_ADDR']);
$api->parseMessage();
?>