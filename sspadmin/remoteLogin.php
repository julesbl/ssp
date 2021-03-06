<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	remoteLogin.php
*   Created:	10/04/2009
*   Descrip:	Logs into the remote domain (currently not functioning).
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

$session = new Protect("admin");

$url = "http://www.localdomain.com/sspadmin/sspapi.php";
$cookieJar = $SSP_Config->cookieJarPath. "jar_". $session->sessionToken. ".txt";
$api = new API_interface($SSP_Config->apiEncryptionKey, $url, $cookieJar);
$api->mirror = true;
$token = $api->sendRemoteLoginSetup("admin", $session->sessionToken, "admin", $_SERVER['REMOTE_ADDR']);
if($api->error){
	echo "Error ". $api->errorString;
}
elseif($token){
	$path = "http://www.remoteDomain.com/sspadmin/index.php?remoteLoginToken=". $token;
	SSP_Divert($path);
}
?>