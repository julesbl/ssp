<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	setup.php
*   Created:	24/10/2008
*   Descrip:	Sets up global variables and configures php.
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
*   Rev. Date	24/10/2008
*   Descrip:	Created.
*/
// Set up global configuration variable.
$SSP_Config = SSP_Configuration::get_configuration();

date_default_timezone_set($SSP_Config->siteTimezoneIdentifier);

// set template path for template routines
define("SFC_FUNCTOKENMAKE", "SSP_Token"); // specify function for form token creation
define("SFC_FUNCTOKENCHECK", "SSP_TokenCheck"); // function to veryify token
define("SFC_FORMSUBMITVARTYPE", "hex"); // data type for form token
define('SSP_TEMPLATEPATHBASE', $SSP_RootPath. $SSP_Config->templateDir);

// Translation configuration
if($SSP_Config->translate){
	require($SSP_IncludePath. 'SSP_translate.php');
	require($SSP_TranslatePath. 'lang_en.conf.php');
	require($SSP_TranslatePath. 'lang_fr.conf.php');
	// add other language files here
	
	// basic language setup
	// start debug mode
	//SSP_translate::debug();
	// configure language translation object
	$SSP_lang = new SSP_translate($SSP_Config->lang, $SSP_TranslatePath);
	
	SSP_checkData::addTranslation($SSP_lang);
	SFC_Form::addTranslation($SSP_lang);
	SSP_Protect::addTranslation($SSP_lang);
}

// set up pages not to be included in the history
SSP_ProtectBase::addNoHistoryPage($SSP_Config->logonScript);
SSP_ProtectBase::addNoHistoryPage($SSP_Config->logoffScript);
SSP_ProtectBase::addNoHistoryPage($SSP_Config->passwordRecover);
SSP_ProtectBase::addNoHistoryPage($SSP_Config->userConfirm);
SSP_ProtectBase::addNoHistoryPage($SSP_Config->newPassword);
SSP_ProtectBase::addNoHistoryPage($SSP_Config->userConfirm);

/**
* Set up PHP initialisation parameters
*
* These can be ignored and set up in the php.ini if you have access
*
* if you need to change the parameters from the default,
* simply uncomment the line and change the required paramater.
*/

// specifies the name of the session which is used as cookie name. It should only contain alphanumeric characters.
 ini_set("session.name", $SSP_Config->sessVarName);

// defines the name of the handler which is used for storing and retrieving data associated with a session.
// files - uses inbuilt php routines, only good for unix systems with small numbers of users
// user - database using abstraction layer.
ini_set("session.save_handler","user");

// specifies the number of seconds after which a session will be seen as 'garbage' and cleaned up. Will also clean up any other temporary tables.
ini_set("session.gc_maxlifetime", $SSP_Config->sessMaxLifetime); // 1440 = 24 minutes

// defines the argument which is passed to the save handler. If you choose the default files handler, this is the path where the files are created. Put in directory your system can access, but not a user with a browser.
ini_set("session.save_path","/usr/local/tmp");

// specifies the probability that the gc (garbage collection) routine is started on each request in percentage. You might want to lower this for busy sites.
ini_set("session.gc_probability","10");

// defines the name of the handler which is used to serialize/deserialize data. Currently, a PHP internal format (name php) and WDDX is supported (name wddx). WDDX is only available, if PHP is compiled with WDDX support. Defaults to php.
// ini_set("session.serialize_handler","php");

// the lifetime of the cookie in seconds which is sent to the browser. The value 0 means "until the browser is closed."
// ini_set("session.cookie_lifetime","0");

// specifies path to set in session_cookie.
 ini_set("session.cookie_path", $SSP_Config->cookiePath);

// specifies the domain to set in session_cookie.
ini_set("session.cookie_domain", $SSP_Config->cookieDomain);

// specifies whether cookies should only be sent over secure connections, also makes the login screen acessed over secure connection
// ini_set("session.cookie_secure","0");

// specifies whether the module will use cookies to store the session id on the client side.
// ini_set("session.use_cookies","1");

// specifies whether the module will only use cookies to store the session id on the client side.
// ini_set("session.use_only_cookies","0");

// contains the substring you want to check each HTTP Referer for. If the Referer was sent by the client and the substring was not found, the embedded session id will be marked as invalid.
// ini_set("session.referer_check","");

// gives a path to an external resource (file) which will be used as an additional entropy source in the session id creation process.
// ini_set("session.entropy_file","");

// specifies the number of bytes which will be read from the file specified above.
// ini_set("session.entropy_length","0");

// default for caching of pages, can be set for each page. Options are none/nocache/private/private_no_expire/public.
// ini_set("session.cache_limiter","nocache");

// session.cache_expire specifies time-to-live for cached session pages in minutes, this has no effect for nocache limiter.
// ini_set("session.cache_expire","180");

// specifies whether the session module starts a session automatically or on request startup. Defaults to 0 (disabled). Please leave this alone.
// ini_set("session.auto_start","0");

// set seperator for SID in urls to &amp; so it will validate properly
ini_set("arg_separator.output","&");

/* End of file setup.php */
/* Location: ./sspincludes/setup.php */