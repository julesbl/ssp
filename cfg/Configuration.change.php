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
	 * database user
	 * @var string  */
	public $dsnUser = null;
	/** 
	 * password for the database
	 * @var string  */
	public $dsnPassword = null;
	/** 
	 * host name
	 * @var string  */
	public $dsnHostName = null;
	/** 
	 * database name
	 * @var string  */
	public $dsnDatabaseName = null;
	/** 
	 * options for the dsn
	 * @var array of string  */
	public $dsnOptions = array('persist=1');
	/** 
	 * name of site, used in error logging etc.
	 * @var string  */
	public $siteName = null;
	/**
	 * Use ssl in site paths
	 * @var bool
	 */
	public $useSSL = null;
	/** 
	 * name shown in emails to users from admin
	 * @var string  */
	public $adminName = null;
	/** 
	 * Email used by the administrator
	 * @var string  */
	public $adminEmail = null;
	/** 
	 * name shown on no reply emails
	 * @var string  */
	public $noReplyName = null;
	/** 
	 * email used for no reply emails
	 * e.g. no-reply@site.com
	 * @var string  */
	public $noReplyEmail = null;
	/** 
	 * domain name with no slashes, www.somesite.com
	 * @var string  */
	public $url = null;
	/**
	 * set the domain for the cookie, if you put ".yourdomain.com" it will be
	 * valid for www.yourdomain.com and yourdomain.com and 
	 * www.subdomain.yourdomain.com
	 * @var string
	 */
	public $cookieDomain = null;
	/**
	 * root of site including HTTP or HTTPS
	 * e.g. http://www.site.com/
	 * @var string
	 */
	public $siteRoot = null;
	/**
	 * name of session public storage instance, usually a cookie
	 * Give it a nice random name, nothing to do with your site name e.g. jumble123, allowed characters are a-zA-Z0-9_ for maximum compatibility
	 * @var string
	 */
	public $sessVarName = null;
	/**
	 * random cookie name, has a rolling random number to make session stealing difficult
	 * Give it a nice random name different from the session name, nothing to do with your site name e.g. sedric123
	 * allowed characters are a-zA-Z0-9_ for maximum compatibility
	 * @var string
	 */
	public $randomCookie = null;
	/** 
	 * remember me cookie name
	 * Give it a nice random name different from the session name, nothing to do with your site name e.g. engleberty123
	 * allowed characters are a-zA-Z0-9_ for maximum compatibility
	 * @var string  */
	public $loginRememberMeCookie = null;
	/**
	 * Prefix for user id generation
	 * nice long sentence 20 - 30 odd chars
	 * @var string
	 */
	public $magicUser = null;
	/**
	 *  ecryption string for users email etc, @todo encryption needs fixing
	 * @var string
	 */
	public $encryptionString = null;
	/**
	 * list of admins to which sql errors are sent]
	 * array('admin@admin.com' => 'Mr Aadmin')
	 * @var array of string
	 */
	public $errorAdmins = null;
	/**
	 * Prefix for token generation
	 * nice long sentence 20 - 30 odd chars
	 * @var string
	 */
	public $magicToken = null;
	/**
	 * enable translation routines
	 * @var bool   */
	public $translate = false;

	/**
	 * Configuration constructor.
	 */
	public function __construct()
	{
		// set up path to templates directory
		$this->templateDir = __DIR__. '/templates/';
		// set up path to translation directory
		$this->translatePath = __DIR__. '/translate/';
		parent::__construct();
	}
}

/* End of file Configuration.php */
/* Location: ./cfg/Configuration.php */