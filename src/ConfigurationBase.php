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
*   Revision:	a
*   Rev. Date	08/01/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	24/10/2008
*   Descrip:	Removed global publiciable and php setup to setup.php.
*
*   Revision:	c
*   Rev. Date	6/01/2016
*   Descrip:	Improved commenting.
*
*   Revision:	d
*   Rev. Date	7/01/2016
*   Descrip:	Changed to psr-4 and became and abstract class.
*/

namespace w34u\ssp;

// Class for Simple Site Protection configuration
abstract class ConfigurationBase
{

	/** 
	 * put site in maintenance mode, just shows a screen
	 * @var bool  */
	public $siteInMaintenanceMode = false;
	/**
	 * Enable site setup script
	 * @var bool
	 */
	public $enableSetup = true;
	/** 
	 * file to be shown if in maintenance mode
	 * @var string  */
	public $siteInMaintenanceScreen = "maintenenceMode.html";
	/** 
	 * list of ip addresses for which maintenance will be ignored
	 * @var array  */
	public $siteInMaintenanceOverrideIps = array(
		"0.0.0.0" => "w34u"
	);

	/** 
	 * database drivers to be used
	 * @var string  */
	public $dsnDatabaseDriver = 'mysqli';
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
	 * database connection string
	 * e.g. mysqli://username:password@hostname/databasename?persist
	 * @var string  */
	public $dsn = null;
	
	/** 
	 * connection encoding
	 * @var string  */
	public $connectionEncoding = "utf8";
	/** 
	 * Table collation, used to create the database tables
	 * @var string  */
	public $tableCollation = 'utf8_general_ci';

	/** 
	 * table used for session data
	 * @var string  */
	public $sessionTable = "SSP_Session";
	/** 
	 * table used for user login and basic info.
	 * @var string  */
	public $userTable = "SSP_UserLogon";
	/** 
	 * used for form tokens, prevents bashing etc.
	 * @var string  */
	public $tokenTable = "SSP_Token";
	/** 
	 * Miscellaneous user data
	 * @var string  */
	public $userMiscTable = "SSP_UserMisc";
	/** 
	 * table for general purpose time tokens
	 * @var string  */
	public $responseTable = "SSP_UserResponse";
	/** 
	 * remember me table
	 * @var string  */
	public $tableRememberMe = "ssp_remember_me";

	// General site information
	/** 
	 * name of site, used in error logging etc.
	 * @var string  */
	public $siteName = null;
	/** 
	 * set the default timezone
	 * @var string  */
	public $siteTimezoneIdentifier = 'Europe/London';
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
	 * Email used for no reply emails
	 * change
	 * @var string  */
	public $noReplyEmail = null;
	/**
	 * Generate html emails for password recovery etc.
	 * @var bool
	 */
	public $htmlEmails = false;
	/** 
	 * fields displayed in SSP_UserAdmin::getName from userMiscTable
	 * @var array  */
	public $displayNameFields = array("FirstName", "FamilyName");
	/** 
	 * display format for SSP_UserAdmin::getName
	 * @var string  */
	public $displayNameFormat = "%s %s";
	/** 
	 * Site encoding for admin
	 * @var string  */
	public $siteEncoding = "UTF-8";

	// General SSP configuration
	/** 
	 * configure to use SSL for login, admin and cookies
	 * @var bool  */
	public $useSSL = null;
	/**
	 * Headers to be sent when in ssl mode
	 * @var array
	 */
	public $sslHeaders = [
		'strict-transport-security' => 'max-age=31536000; includeSubDomains',
		'X-XSS-Protection' => '1; mode=block',
		'X-Content-Type-Options' => 'nosniff',
		'Referrer-Policy' => 'no-referrer-when-downgrade',
		'X-Frame-Options' => 'SAMEORIGIN',
		'Content-Security-Policy' => "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:",
		'X-Content-Security-Policy' => "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:",
		'Feature-Policy' => "geolocation 'none'; midi 'none'; sync-xhr 'none'; microphone 'none'; camera 'none'; magnetometer 'none'; gyroscope 'none'; speaker 'self'; fullscreen 'self'; payment 'none';"
	];
	/**
	 * If in ssl send the securing headers
	 * @var bool
	 */
	public $sslSendHeaders = true;
	/** 
	 * domain name with no slashes, www.somesite.com
	 * change
	 * @var string  */
	public $url = null;
	/** 
	 * http path to the site, generated on construction
	 * @var string  */
	public $pathSite = ""; //
	/** 
	 *  https path to the site, generated on construction
	 * @var string */
	public $pathSiteHttps = "";
	/**
	 * set the doamin for the cookie, if you put ".yourdomain.com" it will be
	 * valid for www.yourdomain.com and yourdomain.com and 
	 * www.subdomain.yourdomain.com
	 * change
	 * @var string
	 */
	public $cookieDomain = null;
	/**
	 * set path for cookie, leave as "/" unless you have a real good reason
	 * @var string
	 */
	public $cookiePath = "/";
	
	// Admin paths setup
	/**
	 * path to admin directory for applications, end with /
	 * @var string
	 */
	public $adminDir = "sspadmin/";
	/**
	 * Path to the templates directory
	 * @var string
	 */
	public $templateDir = null;
	/**
	 * name of logon script in admin
	 * @var string
	 */
	public $logonScript = "sspadmin/user/logon";
	/**
	 * Name of logoff script
	 * @var string
	 */
	public $logoffScript = "sspadmin/user/logoff";
	/**
	 * Path used by email link to login
	 * @var string
	 */
	public $loginByEmailUrl = "sspadmin/user/emaillogin/";
	/**
	 * Password recovery script
	 * @var string
	 */
	public $passwordRecover = "sspadmin/user/passwordrecover";
	/**
	 * password recovery password entry
	 * @var string
	 */
	public $newPassword = "sspadmin/user/newpassword";
	/**
	 * user self admin script
	 * @var string
	 */
	public $userAdminScript = "sspadmin/useradmin";
	/**
	 * administrator admin
	 * @var string
	 */
	public $totalAdminScript = "sspadmin";
	/**
	 * script to create user
	 * @var string
	 */
	public $userCreation = "sspadmin/user/usercreation";
	/**
	 * user confirming, linked to by email
	 * @var string
	 */
	public $userConfirm = "sspadmin/user/userconfirm";
	/**
	 * routine to list users
	 * @var string
	 */
	public $userLister = "sspadmin";
	/**user/logon
	 * root of site including HTTP or HTTPS
	 * change
	 * @var string
	 */
	public $siteRoot = null;
	/**
	 * temporary file directory, should be out of viewable path
	 * @var string
	 */
	public $tempDir = "/usr/local/tmp";
	/**
	 * SSP log file, any errors produced by query errors go here, see debug section.
	 * @var string
	 */
	public $errorLog = null;
	/**
	 * Where the message is to be sent 0 - system log, 1 - emailed to admin, 3 - error log path, 4 - SAPI log handler
	 * @var int
	 */
	public $message_type = 0;
	/** 
	 * default language for site
	 * @var string  */
	public $lang = "en";
	/** 
	 * enable translation routines
	 * @var bool   */
	public $translate = false;
	/**
	 * Translation languages available
	 * @var array of string
	 */
	public $translateLangs = array('en', 'fr');
	/**
	 * Path to translation directory
	 * @var string
	 */
	public $translatePath = null;
	/**
	 * Put translation software into debug mode
	 * @var bool
	 */
	public $translateDebug = false;

	// Divert template configuration, used for automatic diversion to routines using SSP_Divert
	/**
	 * name of automatic diversion template in the template directory
	 * @var string
	 */
	public $divertTemplate = "sspdiverttemplate.tpl";
	/**
	 * Content for divert template
	 * @var array of string
	 */
	public $divertDefaultContent = array(
				"pageTitle" => "Diverting",
				"explanation" => "Diverting to routine",
				"linkText" => "Click here if diversion does not occure within 5 seconds",
			);

	// PHP Session configuration
	// Change this !!!!!
	/**
	 * name of session public storage instance
	 * allowed characters are a-zA-Z0-9_ for maximum compatibility
	 * @var string
	 */
	public $sessVarName = null;
	/**
	 * If a bot is detected scanning the site this session id is used
	 * to prevent abandoned sessions rocketing
	 * @var string
	 */
	public $sessBotDetectionId = 'bot_session';
	/**
	 * preg match string to detect bot scanning the site
	 * @var string
	 */
	public $sessBotDetectionPattern = '/bot|crawl|slurp|spider|mediapartners/i';
	/**
	 * timeout for session in seconds 3600 = 1 hour, session gets cleaned up
	 * @var int  */
	public $sessMaxLifetime = 3600;
	/**
	 * Use strict mode to prevent cookie session attacks
	 * @var int
	 */
	public $sessUseStrictMode = 1;
	/**
	 * Set the session cookie same site to strict to help prevent cross site attacks,
	 * available since 7.3 other option is Lax
	 * @var string
	 */
	public $sessCookieSamesite = 'Strict';
	/**
	 * time after which the login expires if no activity in seconds, 3600 = 1 hour
	 * @var int  */
	public $loginExpiry = 1200;
	/**
	 * allow user to login multiple times (from different machines)
	 * @var bool
	 */
	public $allowMultipleLogin = true;
	/**
	 * Check the IP address stays the same through the sesson
	 * @var bool
	 */
	public $checkIpAddress = true;
	/**
	 * number of octals to check, sometimes need to reduce this to two for aol
	 * @var int
	 */
	public $checkIpAccuracy = 3;
	/** 
	 * accuracy to which IPv6 addresses are checked to be equal
	 * @var int  */
	public $checkIpv6Accuracy = 7;
	/**
	 * All users have fixed IP addresses
	 * @var bool
	 */
	public $fixedIpAddress = false;
	/**
	 * Use another cookie with a random number that changes with each page request
	 * @var bool
	 */
	public $randomCheck = true;
	// Change this !!!!!
	/**
	 * random cookie name, has a rolling random number to make session stealing difficult
	 * allowed characters are a-zA-Z0-9_ for maximum compatibility
	 * @var string
	 */
	public $randomCookie = null;
	/**
	 * Number of random numbers to store for check, so if 2 the current and previous
	 * This was implemented to prevent session failure on fast double click on
	 * slow sites
	 * @var int
	 */
	public $randomCookieChecks = 2;
	/**
	 * User levels for logins
	 * users "public" and "admin" must always exist for the
	 * routines to function, you can have user types with priorities higher
	 * than admin and they will have admin priveleges.
	 * @var array of integers
	 */
	public $userLevels = array(
			"public" => 0,
			"user" => 5,
			"userPlus" => 6,
			"admin" => 31
		);
	/**
	 * level above which user is admin
	 * @var int
	 */
	public $adminLevel = 31;
	/**
	 * Dropdown used in admin user creation and filter in admin
	 * @var array of strings
	 */
	public $userAccessTypeDropdown = array(
			"user" => "Standard User",
			"userPlus" => "Enhanced User",
			"admin" => "Administrator",
		);
	/**
	 * On user creation by non admin this is the default level
	 * @var string
	 */
	public $userDefault = "user";
	/**
	 * standard user joinup has options
	 * @var bool
	 */
	public $userHasSignUpOptions = false;
	/**
	 * Sign up options
	 * @var array of strings
	 */
	public $userAccessSignUpDropdown = array(
			1 => "Standard User",
			2 => "Improved User",
		);
	/**
	 * Sign up levels
	 * @var array of integer
	 */
	public $userAccessSignUpLevels = array(
			1 => "user",
			2 => "userPlus",
		);
	/**
	 * Definition of a valid user by DB flags
	 * @var array of int
	 */
	public $validUserFlags = array(
			"UserDisabled" => 0,
			"UserPending" => 0,
			"UserAdminPending" => 0,
			"CreationFinished" => 1,
			"UserWaiting" => 0,
		);

	// remote auto login configuration (not working at the moment)
	/**
	 * allow remote auto login
	 * @var bool
	 */
	public $autoLoginEnable = false;
	/**
	 * table for remote login setup
	 * @var string
	 */
	public $tableAutoLogin = "SSP_remoteLogin";
	/**
	 * path to slave system
	 * @var string
	 */
	public $remoteSystemApi = "http://www.dcarbon8.com/sspadmin/sspapi.php";
	/**
	 * encryption key 35 chars
	 * @var string
	 */
	public $apiEncryptionKey = "just a little thing, but long enough";
	/**
	 * cookie jar path, not used at the moment
	 * @var string
	 */
	public $cookieJarPath = "/usr/local/tmp";
	/**
	 * enable http login stuff for curl
	 * @var string
	 */
	public $httpLogin = false;
	/**
	 * user name for http login
	 * @var string
	 */
	public $httpUser = "";
	/**
	 * password for http login
	 * @var string
	 */
	public $httpPasword = "";

	// login configuration publiciables/join
	// ,
	// 
	/**
	 * type of login 0 - email/password, 1 - username/password
	 * 2 - user defiined
	 * @var int
	 */
	public $loginType = 0;
	/**
	 * If enabled allows login by email
	 * @var bool
	 */
	public $loginByEmail = false;
	/**
	 * If true the default login will be by email
	 * @var bool
	 */
	public $loginByEmailDefault = false;
	/**
	 * Types of user logins that can use the email option
	 * @var array
	 */
	public $loginByEmailUserTypes = ['user'];
	/**
	 * Time for which the login email link is valid in seconds
	 * 3600 - 1 hour
	 * @var int
	 */
	public $loginByEmailTimeout = 3600;
	/**
	 * reload login email or user name on failure
	 * @var bool
	 */
	public $reLoadOnLoginFail = true;
	/**
	 * delay after logon fail in seconds before displaying login screen again, careful of browser timeouts
	 * @var int
	 */
	public $logonFailDelay = 2;
	/**
	 * Enable two factor authentication
	 * @var bool
	 */
	public $twoFactorAuthentication = false;
	/**
	 * Enable the public user joinup script
	 * @var bool
	 */
	public $enableUserJoin = false;
	/**
	 * admin must vet each new member
	 * @var bool
	 */
	public $adminCheck = false;
	/**
	 * what the program does to confirm user
	 * 0 - new user is instantly a member
	 * 1 - emailed random password which can be changed
	 * 2 - emailed a link, prompted for password
	 * 3 - emailed a link, enables the account
	 * @var int
	 */
	public $confirmType = 2;
	/**
	 * User is instantly logged in after joining.
	 * Only works for $adminCheck = false and $confirmType = 0
	 * @var boolean
	 */
	public $joinLoggedIn = false;
	/**
	 * How long the user has to click on the link in seconds
	 * 604800 = 7 days
	 * @var int
	 */
	public $confirmExpiry = 604800;

	// remember me fuctionality for the login
	/** 
	 * enable remember me functionality
	 * @var bool  */
	public $loginRememberMe = true;
	/** 
	 * number of days the remember me cookie lasts
	 * @var int  */
	public $loginRememberMePeriod = 100;

	// Change this !!!!!
	/** 
	 * remember me cookie name
	 * allowed characters are a-zA-Z0-9_ for maximum compatibility
	 * @var string  */
	public $loginRememberMeCookie = null;
	
	/**
	 * Needs/join such things as credit card confirm
	 * @var bool
	 */
	public $furtherProgram = false;
	/**
	 * gets user name anyway during user creation
	 * @var bool
	 */
	public $getUserName = false;
	/**
	 * Go to a particular page after login
	 * @var bool
	 */
	public $loginAfterGoToPage = false;
	/**
	 * Page to go to after login
	 * @var string
	 */
	public $loginAfterGoToPageUrl = null;

	/**
	 * Prefix for user id generation
	 * change
	 * @var string
	 */
	public $magicUser = null;
	// Change this !!!!!
	/**
	 *  ecryption string for users email etc, @todo encryption needs fixing
	 * @var string
	 */
	public $encryptionString = null;
	/**
	 * encrypt email
	 * @var bool
	 */
	public $useEncryption = false;
	/**
	 * allow duplicate emails in the login table, only works for type 1
	 * @var bool
	 */
	public $allowDuplicateEmails = false;
	/**
	 * one way encryption on password
	 * @var bool
	 */
	public $encryptPassword = true;
	/**
	 * Minimum length of password
	 * @var int
	 */
	public $minPassword = 8;

	/**
	 * 0 - prompt for new password, 1 - send password to user, only works for  unencrypted passwords
	 * @var int
	 */
	public $passwordRecovery = 0;
	/**
	 * Time before password recover email becomes invalid in seconds
	 * @var int
	 */
	public $recoverTime = 3600;

	// User admin configuration
	/**
	 * Filed to which the alpha sorting is applied
	 * @var string 
	 */
	public $defaultAlpha = "FamilyName";
	/**
	 * fields available for searching m. - misc data, u. - login user data
	 * @var string
	 */
	public $fieldsFilterList = array(
		"m.FirstName" => "First Name", 
		"m.FamilyName" => "Family Name", 
		"m.TownCity" => "Town or City",
		"u.UserEmail" => "Email",
		);
	/**
	 * default number of/join lines per page
	 * @var int
	 */
	public $limit = 25;
	/**
	 * options for results per page
	 * @var array of int
	 */
	public $limits = array('25'=>'25','50'=>'50','100'=>'100','200'=>'200');
	
	// Debug settings
	/**
	 * display backtrace as part of error
	 * @var boolean
	 */
	public $errorDisplayBacktrace = false;
	/**
	 * display login debug info.
	 * @var bool
	 */
	public $loginDebug = false;
	/**
	 * display any faults which caused a diversion to the login page
	 * @var bool
	 */
	public $accessFaultDebug = false;
	/**
	 * if true the divert page is always shown wiht no diversion
	 * @var bool
	 */
	public $divertDebug = false;
	/**
	 * if true display sql faults on screen, else send to error log
	 * @var bool
	 */
	public $displaySqlFaults = false;
	/**
	 * IP address to which to show the debug
	 * @var string
	 */
	public $debugIP = "0.0.0.0";
	/**
	 * enable IP debug checking, if false will show the debug to all
	 * @var bool
	 */
	public $checkDebugIp = false;
	/**
	 * list of admins to which sql errors are sent]
	 * change
	 * 'admin@admin.com' => 'Mr Aadmin'
	 * @var array of string
	 */
	public $errorAdmins = null;

	// display configurations
	/**
	 * Class added to elelments displaying error in admin
	 * @var string
	 */
	public $errorTextClass = "SSPErrorText";

	// Form generation setup
	/**
	 * Prefix for token generation
	 * change
	 * @var string
	 */
	public $magicToken = null;
	/**
	 * clean up any tokens older than this, seconds
	 * @var int
	 */
	public $tokenClean = 1800;
	/**
	 * Name of field used in forms for token passing
	 * @var string
	 */
	public $tokenName = "SSPToken";
	
	/**
	 * Static configuration
	 * @var Configuration
	 */
	private static $cfg = null;
	
	/**
	 * Session name
	 * @var string 
	 */
	private static $sessionName = null;
	/**
	 * Properties that are checked on object creation to be non null
	 * @var string[]
	 */
	private static $checkProperties = array(
		'dsnDatabaseName', 'siteName', 'adminName', 'adminEmail', 'noReplyName', 'noReplyEmail', 'useSSL', 'url', 'cookieDomain', 'siteRoot', 'sessVarName',
		'randomCookie', 'loginRememberMeCookie', 'magicUser', 'encryptionString', 'errorAdmins',
		'magicToken', 'templateDir'
	);

	// constructor for configuration class
	public function __construct() {
		
		$this->generateDSN();

		$this->checkProperties();
		
	   // build paths to scripts
		$this->pathSite = "http://". $this->url. "/";
		$this->pathSiteHttps = "https://". $this->url. "/";
		
		// set mutibyte encoding
		mb_internal_encoding($this->siteEncoding);
		
		if($this->useSSL){
			// absolute path for ssl
			$this->adminDir = $this->pathSiteHttps. $this->adminDir;
			$path = $this->pathSiteHttps;
		}
		else{
			$this->adminDir = $this->pathSite. $this->adminDir;
			$path = $this->pathSite;
		}
		$this->logonScript = $path. $this->logonScript;
		$this->logoffScript = $path. $this->logoffScript;
		$this->loginByEmailUrl = $path. $this->loginByEmailUrl;
		$this->passwordRecover = $path. $this->passwordRecover;
		$this->newPassword = $path. $this->newPassword;
		$this->userAdminScript = $path. $this->userAdminScript;
		$this->totalAdminScript = $path. $this->totalAdminScript;
		$this->userCreation = $path. $this->userCreation;
		$this->userConfirm = $path. $this->userConfirm;
		$this->userLister = $path. $this->userLister;

		// configure debug
		if($this->checkDebugIp){
			if(SSP_paddIp($this->debugIP) === SSP_paddIp($_SERVER['REMOTE_ADDR'])){
				$debug = true;
			}
			else{
				$debug = false;
			}
		}
		else{
			$debug = true;
		}
		$this->loginDebug = $debug && $this->loginDebug;
		$this->accessFaultDebug = $debug && $this->accessFaultDebug;
		$this->divertDebug = $debug && $this->divertDebug;
		$this->displaySqlFaults = $debug && $this->displaySqlFaults;
		
		date_default_timezone_set($this->siteTimezoneIdentifier);
		
		Protect::setTemplatePath($this->templateDir);
		
		// Translation configuration
		if($this->translate){
			// basic language setup
			// start debug mode
			if($this->translateDebug){
				Translate::debug();
			}
			// configure language translation object
			$SSP_lang = new Translate($this->lang, $this->translateLangs, $this->translatePath);

			CheckData::addTranslation($SSP_lang);
			sfc\Form::addTranslation($SSP_lang);
			Protect::addTranslation($SSP_lang);
		}

		// set up pages not to be included in the history
		ProtectBase::addNoHistoryPage($this->logonScript);
		ProtectBase::addNoHistoryPage($this->logoffScript);
		ProtectBase::addNoHistoryPage($this->loginByEmailUrl);
		ProtectBase::addNoHistoryPage($this->passwordRecover);
		ProtectBase::addNoHistoryPage($this->userConfirm);
		ProtectBase::addNoHistoryPage($this->newPassword);
		ProtectBase::addNoHistoryPage($this->userCreation);

		/**
		* Set up PHP initialisation parameters
		*
		* These can be ignored and set up in the php.ini if you have access
		*
		* if you need to change the parameters from the default,
		* simply uncomment the line and change the required paramater.
		*/

		// specifies the name of the session which is used as cookie name. It should only contain alphanumeric characters.
		 ini_set("session.name", $this->sessVarName);

		// defines the name of the handler which is used for storing and retrieving data associated with a session.
		// files - uses inbuilt php routines, only good for unix systems with small numbers of users
		// user - database using abstraction layer.
		//ini_set("session.save_handler","user");

		// specifies the number of seconds after which a session will be seen as 'garbage' and cleaned up. Will also clean up any other temporary tables.
		ini_set("session.gc_maxlifetime", $this->sessMaxLifetime); // 1440 = 24 minutes

		// defines the argument which is passed to the save handler. If you choose the default files handler, this is the path where the files are created. Put in directory your system can access, but not a user with a browser.
		ini_set("session.save_path","/usr/local/tmp");

		// specifies the probability that the gc (garbage collection) routine is started on each request in percentage. You might want to lower this for busy sites.
		ini_set("session.gc_probability","10");

		// defines the name of the handler which is used to serialize/deserialize data. Currently, a PHP internal format (name php) and WDDX is supported (name wddx). WDDX is only available, if PHP is compiled with WDDX support. Defaults to php.
		// ini_set("session.serialize_handler","php");

		// the lifetime of the cookie in seconds which is sent to the browser. The value 0 means "until the browser is closed."
		// ini_set("session.cookie_lifetime","0");

		// specifies path to set in session_cookie.
		 ini_set("session.cookie_path", $this->cookiePath);

		// specifies the domain to set in session_cookie.
		ini_set("session.cookie_domain", $this->cookieDomain);

		// turn on session strict mode
		ini_set("session.use_strict_mode", $this->sessUseStrictMode);

		// set same site session variable to strict if it exists, only available 7.3 or gt
		if(ini_get('session.cookie_samesite') !== false){
			ini_set('session.cookie_samesite', $this->sessCookieSamesite);
		}
		
		if($this->useSSL){
			ini_set("session.cookie_secure", 1);
			ini_set('session.cookie_httponly', 1);
		}
	}
	
	/**
	 * Check all required properties have been configured
	 */
	private function checkProperties(){
		$paramOk = true;
		foreach(self::$checkProperties as $property){
			if(is_null($this->$property)){
				$paramOk = false;
				trigger_error('Property '. $property.
				' of the configuration object has not been assigned a value, see '.
				implode(', ', self::$checkProperties). ' on line 703 of ConfigurationBase', E_USER_ERROR);
			}
		}
		if($this->translate){
			if(is_null($this->translatePath)){
				$paramOk = false;
				trigger_error('Property '. 'translatePath'.
				' of the configuration object has not been assigned a value, see '.
				implode(', ', self::$checkProperties). ' on line 691 of ConfigurationBase', E_USER_ERROR);
			}
		}
		if(!$paramOk){
			exit();
		}
	}
	
	/**
	 * Get the dsn for the database
	 * Useful for generating the DSN when the database options come from an
	 * esternal system or have special characters within them.
	 */
	private function generateDSN(){
		if($this->dsn === null){
			$dsn = $this->dsnDatabaseDriver. '://'. urlencode($this->dsnUser). ':'. urlencode($this->dsnPassword). '@'. $this->dsnHostName. '/'. $this->dsnDatabaseName;
			if(count($this->dsnOptions)){
				$dsn .= '?'. implode('&', $this->dsnOptions);
			}
			$this->dsn = $dsn;
		}
	}
	
	/**
	 * Get the, and if neccessary, create the configuration
	 * @return \w34u\ssp\Configuration
	 */
	public static function getConfiguration(){
		if(self::$cfg === null){
			$className = get_called_class();
			self::$cfg = new $className();
		}
		return self::$cfg;
	}
	
	/**
	 * Save the session name
	 * @param string $sessionName
	 */
	public static function setSessionName($sessionName){
		self::$sessionName = $sessionName;
	}
	
	/**
	 * Return the session name
	 * @return string
	 */
	public static function getSessionName(){
		return self::$sessionName;
	}
}
/* End of file ConfigurationBase.php */
/* Location: ./src/ConfigurationBase.php */