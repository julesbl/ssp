<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    Simple Site Protection
*   Routine:	includeheader.php
*   Created:	12/02/2005
*   Descrip:	Include file to set include paths for admin applications.
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
*   Rev. Date	12/02/2005
*   Descrip:	Created.
*   
*   Revision:	b
*   Rev. Date	13/02/2009
*   Descrip:	changed the way the includes are done.
*/

if(strpos($_SERVER["SERVER_NAME"],"w34u") !== false){
    $devServer = true;
	$server = "w34u";
}
elseif(strpos($_SERVER["SERVER_NAME"],"localhost") !== false){
    $devServer = true;
	$server = "localhost";
}
else{
    $devServer = false;
	$server = "";
}
if($server == "w34u"){
    // Set up absolute path to site root directory (not publicly viewable area if different)
    $SSP_RootPath = "/home/julesb/webapps/ssp/";
    // Set up absolute path to includes directory, to be modified by user, best put outside the browser observable part of the site.
    $SSP_IncludePath = $SSP_RootPath. "sspincludes/";
	// Set up abosolute path to translation directory
	$SSP_TranslatePath = $SSP_RootPath. "translate/";
}
elseif($server == "localhost"){
    // Set up absolute path to site root directory (not publicly viewable area if different)
    $SSP_RootPath = "/home/julianb/MyDocuments/WebSites/PHPDevelopment/SSP_SimpleSiteProtection/working/";
    // Set up absolute path to includes directory, to be modified by user, best put outside the browser observable part of the site.
    $SSP_IncludePath = $SSP_RootPath. "sspincludes/";
	// Set up abosolute path to translation directory
	$SSP_TranslatePath = $SSP_RootPath. "translate/";
}
else{
    // Set up absolute path to site root directory (not publicly viewable area if different)
    $SSP_RootPath = "";
    // Set up absolute path to includes directory, to be modified by user, best put outside the browser observable part of the site.
    $SSP_IncludePath = $SSP_RootPath. "includes/";
	// Set up abosolute path to translation directory
	$SSP_TranslatePath = $SSP_RootPath. "translate/";
}
require($SSP_IncludePath. "include.php");

?>