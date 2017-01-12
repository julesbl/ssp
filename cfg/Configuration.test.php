<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	ConfigurationBase.php
*   Created:	08/01/2005
*   Descrip:	Sets up basic configuration for the application.
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
*   You should have received a copy of the The MIT License (MIT)
*   along with SSP; if not, view at
*   http://www.opensource.org; https://opensource.org/licenses/MIT
*
*/

namespace w34u\ssp;

/*
 * The properties named in checkProperties need to be
 * defined for the system to work
 */
class Configuration extends ConfigurationBase
{
	/**
	 * Enable site setup script
	 * @var bool
	 */
	public $enableSetup = true;
	/** 
	 * database drivers to be used
	 * @var string  */
	public $dsnDatabaseDriver = 'mysqli';
	/** 
	 * database user
	 * @var string  */
	public $dsnUser = 'ssptest';
	/** 
	 * password for the database
	 * @var string  */
	public $dsnPassword = 'ssptest';
	/** 
	 * host name
	 * @var string  */
	public $dsnHostName = 'localhost';
	/** 
	 * database name
	 * @var string  */
	public $dsnDatabaseName = 'ssptest';
	/** 
	 * options for the dsn
	 * @var array of string  */
	public $dsnOptions = array('persist=1');
	/** 
	 * name of site, used in error logging etc.
	 * @var string  */
	public $siteName = "SSP Testing";
	/** 
	 * name shown in emails to users from admin
	 * @var string  */
	public $adminName = "SSP Admin";
	/** 
	 * Email used by the administrator
	 * @var string  */
	public $adminEmail = "info@w34u.com";
	/** 
	 * name shown on no reply emails
	 * @var string  */
	public $noReplyName = "No Reply";
	
	public $noReplyEmail = 'no-reply@w34u.com';
	
	public $url = 'localhost:8080';
	
	public $cookieDomain = ".localhost";
	
	public $siteRoot = "http://localhost:8080/";
	
	public $sessVarName = "ssptesting";
	
	public $randomCookie = 'ssptestingrndcookie';
	
	public $loginRememberMeCookie = "ssptestinremeberme";
	
	public $magicUser = "ssptestingmagicuser";
	
	public $encryptionString = "testiung string for making good encyption";
	
	public $errorAdmins = array(
		"j.blundell@w34u.com" => "Julian Blundell",
	);

	public $magicToken = "another randomish string to help creaste tokens";
	/**
	 * Path to the templates directory
	 * @var string
	 */
	public $templateDir = "/../cfg/templates/";
	/** 
	 * enable translation routines
	 * @var bool   */
	public $translate = true;
	/**
	 * Path to translation directory
	 * @var string
	 */
	public $translatePath = "/../cfg/translate/";
	
	public $displaySqlFaults = true;
	
	public function __construct() {
		parent::__construct();
		
		$this->loginDebug = true;
		$this->divertDebug = false;
		$this->htmlEmails = true;
		
		$this->enableUserJoin = true;
		$this->adminCheck = true;
		
	}
	
}

/* End of file Configuration.php */
/* Location: ./cfg/Configuration.php */