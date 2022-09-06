<?php
/**
 *   Site by w34u
 *   http://www.w34u.com
 *   info@w34u.com
 *   +44 (0)7833 512221
 *
 *   Project:    Simple Site Protection
 *   Routine:    SSP_ProtectBase.php
 *   Created:    07/01/2005
 *   Descrip:    Base classes for used to run the SSP system.
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
 *   Revision:    a
 *   Rev. Date    07/01/2005
 *   Descrip:    Created.
 *
 *   Revision:    b
 *   Rev. Date    19/08/2007
 *   Descrip:    Language and country added as optional vars in SSP_Protect,
 *   looks for lang and country fields in the login table. Also big bug in the return
 *    return to logged in page fixed.
 *
 *   Revision:    c
 *   Rev. Date    23/02/2011
 *   Descrip:    Changed to php5 class system.
 *
 *   Revision:    d
 *   Rev. Date    08/07/2015
 *   Descrip:    Changed file name and removed login base class to another file.
 */

namespace w34u\ssp;

abstract class ProtectBase
{
	// Class to be instanciated at the top of each page.
	// Protects the page or keeps the session going in public pages.

	/** @var string session name for current laguage code */
	const LANGSESSIONVAR = 'SSP_currentLanguageCode';
	/** @var string session name for current language detected */
	const LANGCODEDETECTEDVAR = 'SSP_languageCodeDetected';

	/**
	 * short user name for login, boards etc.
	 * @var string
	 */
	public $userName = "";
	/**
	 * users access level for protection.
	 * @var string
	 */
	public $userAccessLevel = "";
	/**
	 * users email
	 * @var string
	 */
	public $userEmail = "";
	/**
	 * unique id used to identify this user
	 * @var string
	 */
	public $userId = "";
	/**
	 * user is logged in
	 * @var bool
	 */
	public $loggedIn = false;
	/**
	 * user is admin level
	 * @var bool
	 */
	public $admin = false;
	/**
	 * SID being used by the users session
	 * @var string
	 */
	public $sessionToken;
	/**
	 * country for user
	 * @var string
	 */
	public $country;
	/**
	 * full user information object
	 * @var object
	 */
	public $userInfo;
	/**
	 * full misc info object
	 * @var object
	 */
	public $miscInfo;
	/**
	 * logged in session fault
	 * @var bool
	 */
	public $error = false;
	/**
	 * User configuration fault
	 * @var bool
	 */
	private $userFault = false;
	/**
	 * User needs higher login
	 * @var bool
	 */
	private $needHigherLogin = false;
	/**
	 * Page access level
	 * @var int
	 */
	private $pageAccess = 0;
	/**
	 * list of errors
	 * @var array
	 */
	public $errors = array();
	/**
	 * language code for translation
	 * @var string
	 */
	public $lang;

	/**
	 * config object
	 * @var Configuration
	 */
	public $cfg;
	/**
	 * database object
	 * @var SspDB
	 */
	public $db;
	/**
	 * Local configuration
	 * @var ProtectConfig
	 */
	private $config = false;

	/**
	 * Attempt to translate the text
	 * @var bool attempt to translate the text
	 */
	private static $translate = false;
	/**
	 * translator object use to translate strings
	 * @var Translate
	 */
	public static $tranlator;
	/**
	 * name of method used in tranlation object
	 * @var string
	 */
	private static $translateMethod = 't';
	/**
	 * Path to template directory
	 * @var string
	 */
	private static $templatePath = '';

	/**
	 * List of pages which are not included in the history
	 * @var array
	 */
	private static $noHistoryOnPages = array();


	/**
	 * Constructor
	 * @param string $pageAccessLevel - users allowed to access the page
	 * @param bool $pageCheckEquals - if true only this user type can access this page
	 * @param bool $doHistory - do history for this page
	 * @param ProtectConfig $config - Protected session configuration options
	 */
	public function __construct($pageAccessLevel = "", $pageCheckEquals = false, $doHistory = true, $config = null)
	{
		if ($config === null) {
			$this->config = new ProtectConfig();
		} else {
			$this->config = $config;
		}

		$this->cfg = Configuration::getConfiguration();
		$this->db = SspDb::getConnection();

		// send ssl security headers
		$this->sendSslHeaders();

		// set up db session handling
		$this->startSession();

		$this->setupLanguage();

		$this->maintenanceMode();

		// turn off sql cacheing if it is set, but preserve the status to turn it back on after
		if ($this->db->cache) {
			$queryResultCacheing = true;
			$this->db->cache = false;
		} else {
			$queryResultCacheing = false;
		}

		$pageAccessLevel = $this->checkParameters($pageAccessLevel, $pageCheckEquals);

		if (!empty($this->config->loginContent)) {
			$_SESSION["SSP_LoginPageAddtionalContent"] = $this->config->loginContent;
		}

		// check https:// site, and if fail divert to correct url
		if ($this->cfg->useSSL or $this->config->forceSSLPath) {
			if (!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == "off") {
				// script not called using https
				SSP_Divert(SSP_Path(true, true));
			}
		}

		$this->country = "";

		// do any external routines before history is called
		$this->autoLogin();

		if ($doHistory) {
			$this->pageHistory();
		}

		$this->processSession($pageAccessLevel, $pageCheckEquals);

		// handle user faults
		$this->userFaultHandling($queryResultCacheing);

		// final setup of page
		$this->finalSetup();

		// restore query cacheing mode
		$this->db->cache = $queryResultCacheing;
	}

	/**
	 * Start session handling
	 */
	private function startSession()
	{
		// Check if session is already started
		if(session_status() === PHP_SESSION_ACTIVE){
			return;
		}
		$handler = new SessionHandler();

		session_set_save_handler(
			array($handler, 'open'),
			array($handler, 'close'),
			array($handler, 'read'),
			array($handler, 'write'),
			array($handler, 'destroy'),
			array($handler, 'gc')
		);

		// the following prevents unexpected effects when using objects as save handlers
		register_shutdown_function("session_write_close");

		session_start();
	}

	/**
	 * Process session user stuff
	 * @param string $pageAccessLevel - page access string
	 * @param bool $pageCheckEquals - user has to be of the page level
	 */
	private function processSession($pageAccessLevel, $pageCheckEquals)
	{
		// get all session information for valid sessions
		$query = sprintf("select * from %s where %s = ? and %s = ?",
			$this->cfg->sessionTable,
			$this->db->qt("SessionId"),
			$this->db->qt("SessionName")
		);
		$values = array(session_id(), session_name());
		$this->db->query($query, $values, "SSP session handling: Get session information");

		if ($this->db->numRows() > 0) {
			// get result if existing session
			$sessionInfo = $this->db->fetchRow();
			if (trim($sessionInfo->UserId) != "") {
				$userFault = $this->getUser($sessionInfo);
			}
		} else {
			$this->log("New session started");
		}

		// process user information if logged in.
		$pageAccess = $this->cfg->userLevels[$pageAccessLevel];
		$this->pageAccess = $pageAccess;
		if ($this->loggedIn) {
			if (!$this->validUserFlags()) {
				$this->userFault = true;
			} elseif ($this->cfg->userLevels[$this->userInfo->UserAccess] < $pageAccess) {
				// user does not have a high enough access level
				$this->userFault = true;
				$this->needHigherLogin = true; // flag higher login needed
				$this->log("User Access level not high enough Level: " . $this->userInfo->UserAccess . " " . $this->cfg->userLevels[$this->userInfo->UserAccess] . " Page " . $pageAccess);
			} elseif ($pageCheckEquals and ($this->cfg->userLevels[$this->userInfo->UserAccess] != $pageAccess)) {
				// user does not have the correct user access level
				$this->userFault = true;
				$this->needHigherLogin = true; // flag different login needed
				$this->log("User Access level not equal to the page's level");
			} elseif ($this->cfg->checkIpAddress and SSP_trimIp($sessionInfo->SessionIp) !== SSP_trimIp($_SERVER["REMOTE_ADDR"])) {
				// users IP address has changed
				$this->userFault = true;
				$this->log("User IP address changed " . SSP_paddIp($_SERVER["REMOTE_ADDR"]));
			} elseif (($this->cfg->fixedIpAddress or $this->userInfo->UserIpCheck) and SSP_paddIp($sessionInfo->SessionUserIp) !== SSP_paddIp($_SERVER["REMOTE_ADDR"])) {
				// user is at incorrect IP address
				$this->userFault = true;
				$this->log("User IP address incorrect, UserIP: " . SSP_paddIp($sessionInfo->SessionUserIp) . " Remote IP: " . SSP_paddIp($_SERVER["REMOTE_ADDR"]));
			}

			if ($this->chackRandomFault($sessionInfo)) {
				$this->userFault = true;
			}

		} else {
			$this->log("User not logged in");
		}

	}

	/**
	 * @param \stdClass $sessionInfo - session information
	 * @return bool - true on fault
	 */
	private function getUser($sessionInfo)
	{
		$where = ["UserId" => $sessionInfo->UserId];
		$this->userInfo = $this->db->get($this->cfg->userTable, $where, "SSP Session: getting login data");

		if ($this->db->numRows()) {
			// user found

			// check for login expiry
			if ($sessionInfo->SessionTime + $this->cfg->loginExpiry > time()) {
				$this->loggedIn = true;
				$this->userId = $this->userInfo->UserId;
				$this->userName = $this->userInfo->UserName;
				$this->userAccessLevel = $this->userInfo->UserAccess;
				if ($this->cfg->userLevels[$this->userAccessLevel] >= $this->cfg->adminLevel) {
					// admin user
					$this->admin = true;
				}
				$this->userEmail = SSP_decrypt($this->userInfo->UserEmail, $this->cfg->useEncryption);
				if (isset($this->userInfo->country) and trim($this->userInfo->country) != "") {
					$this->country = $this->userInfo->country;
				}
				return false;
			} else {
				$this->log("Login expired");
				$this->loggedIn = false;
				$this->db->update($this->cfg->sessionTable, array('UserId' => ''), array('SessionId' => session_id(), 'SessionName' => session_name()), 'SSP Session: clearing user id from expired login');
				return true;
			}
		} else {
			$this->log("User not found from ID");
			return true;
		}
	}

	/**
	 * Check user flags are valid
	 * @return bool - true on user flags ok
	 */
	private function validUserFlags()
	{
		foreach ($this->cfg->validUserFlags as $flagName => $validFlagValue) {
			if ($this->userInfo->{$flagName} != $validFlagValue) {
				$this->log("Invalid user flag " . $flagName . " value required: " . $validFlagValue . " actual: " . $this->userInfo->{$flagName});
				return false;
			}
		}
		return true;
	}

	/**
	 * Send ssl security headers
	 */
	private function sendSslHeaders()
	{
		if ($this->cfg->useSSL and $this->cfg->sslSendHeaders and $this->config->sslSendHeaders) {
			$headers = $this->cfg->sslHeaders;
			if($this->cfg->devHeaders === true){
				$headers = $this->cfg->devSslHeaders;
			}
			foreach ($headers as $header => $string) {
				header($header . ': ' . $string);
			}
		}
	}

	/**
	 * Check the session rolling number cookie
	 * @param object $sessionInfo - session record
	 * @return boolean - true if fails
	 */
	private function chackRandomFault($sessionInfo)
	{
		if (!$this->cfg->randomCheck or $this->config->noCookieUpdate) {
			// return ok if not configured
			return false;
		}
		// do checking of random number cookie
		if (isset($_COOKIE[$this->cfg->randomCookie])) {
			// Cookie exists
			$randomsStored = explode(',', $sessionInfo->SessionRandom);
			if (in_array($_COOKIE[$this->cfg->randomCookie], $randomsStored) !== false) {
				// Numbers match, generate next number and set cookie and session table.

				$randomCookie = mt_rand(0, 100000);
				$options = [
					'expires' => 0,
					'path' => $this->cfg->cookiePath,
					'domain' => $this->cfg->cookieDomain,
					'secure' => $this->cfg->useSSL,
					'samesite' => 'Strict'
				];
				setcookie($this->cfg->randomCookie, $randomCookie, $options);

				$randomsStored[] = $randomCookie;
				if (count($randomsStored) > $this->cfg->randomCookieChecks) {
					array_shift($randomsStored);
				}
				$fields = array("SessionRandom" => implode(',', $randomsStored));
				$where = array("SessionId" => $sessionInfo->SessionId);
				$this->db->update($this->cfg->sessionTable, $fields, $where, "SSP Session: Set Random Cookie failed");
				return false;
			} else {
				$this->log("Random cookie not the same");
			}
		} else {
			$this->log("Random cookie does not exist");
		}
		return true;
	}

	/**
	 * Handle user faults
	 * @param boolean $queryResultCacheing - query caching on or off
	 */
	private function userFaultHandling($queryResultCacheing)
	{
		// user fault detected in current session other than needing a higher login
		$logonDivertContent = array(
			"pageTitle" => $this->t("Diverting to logon"),
			"linkText" => $this->t("Click here to login if divert does not happen within 5 seconds")
		);
		if ($this->pageAccess > 0 and $this->userFault and !$this->needHigherLogin) {
			$this->error = true;
			// kill the login
			$this->killLogin();
			$this->loggedIn = false;
			session_write_close(); // ensure the session varibles are updated
			$explanation = $this->t("General protection fault");
			if ($this->cfg->accessFaultDebug) {
				// Add fault data to divert page
				$explanation .= "<br />";
			}
			$this->db->cache = $queryResultCacheing;
			if ($this->config->noLoginDivert or SSP_isAjaxCall()) {
				echo $this->config->noLoginDivertText;
				exit();
			} else {
				$this->goToLogin($explanation);
			}
		} elseif ($this->pageAccess > 0 and (!$this->loggedIn or $this->needHigherLogin)) {
			// logged on user needs a higher or different login session or no user logged in
			session_write_close(); // ensure the session varibles are updated
			// divert to logon script
			$explanation = $this->t("Access control fault");
			if ($this->cfg->accessFaultDebug) {
				// Add fault data to divert page
				$explanation .= "<br />";
			}
			$this->db->cache = $queryResultCacheing;
			if ($this->config->noLoginDivert or SSP_isAjaxCall()) {
				echo $this->config->noLoginDivertText;
				exit();
			} else {
				$this->goToLogin($explanation);
			}
		} elseif ($this->pageAccess == 0 and $this->userFault) {
			// fault detected in user logon on public page
			// display fault if configured
			if ($this->cfg->accessFaultDebug) {
				// Add fault data to divert page
				$logonDivertContent["explanation"] .= "<br />";
			}
			$this->error = true;
			// kill the login
			$this->killLogin();
			$this->loggedIn = false;
		}
	}

	/**
	 * Do final user setup and page config
	 */
	private function finalSetup()
	{
		// set up final properties
		$this->sessionToken = session_ID();
		if ($this->loggedIn) {
			$this->succesfullLoginSessionCheck();
		}

		// send page cacheing parameters
		if (!$this->config->pageCaching) {
			// Date in the past
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

			// always modified
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

			// HTTP/1.1
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);

			// HTTP/1.0
			header("Pragma: no-cache");
		}
		// send time for page valid if time specified is greater than 0
		if ($this->config->pageValid > 0) {
			header("Expires: " . gmstrftime("%a, %d %b %Y %H:%M:%S", gmmktime() + $this->config->pageValid) . " GMT");
		}
	}

	/**
	 * Set up internationalisation
	 */
	private function setupLanguage()
	{
		if ($this->cfg->translate) {
			// save language code
			if (!isset($_SESSION[self::LANGSESSIONVAR])) {
				$_SESSION[self::LANGSESSIONVAR] = $this->cfg->lang;
			}
			$this->lang =& $_SESSION[self::LANGSESSIONVAR];

			// attempt browser language detection if first load by user
			if (!isset($_SESSION[self::LANGCODEDETECTEDVAR])) {
				$this->lang = self::$tranlator->detectBrowserLanguage();
				$_SESSION[self::LANGCODEDETECTEDVAR] = 1;
			}
			// add another template path as priority for the new selected language
			if ($this->lang != $this->cfg->lang) {
				self::$tranlator->setLanguage($this->lang);
				Template::addPath(self::$templatePath . $this->lang . '/');
			}
		}

		// set up template path for default language
		Template::addPath(self::$templatePath . $this->cfg->lang . '/');

		// load general language file for login and protection routines
		if ($this->cfg->translate) {
			self::$tranlator->loadFile(false, 'login');
		}
	}

	/**
	 * check for maintenance mode
	 */
	private function maintenanceMode()
	{
		if ($this->cfg->siteInMaintenanceMode) {
			$siteMaintenenceMode = true;
			foreach ($this->cfg->siteInMaintenanceOverrideIps as $ipNumber => $name) {
				if (SSP_paddIp($ipNumber) == SSP_paddIp($_SERVER["REMOTE_ADDR"])) {
					$siteMaintenenceMode = false;
					break;
				}
			}
			if ($siteMaintenenceMode) {
				SSP_Divert($this->cfg->siteRoot . $this->cfg->siteInMaintenanceScreen);
			}
		}
	}

	/**
	 * Check the parameters are correct
	 * @param string $pageAccessLevel - page access user level
	 * @param bool $pageCheckEquals - only that user level
	 * @return string - page access level
	 */
	private function checkParameters($pageAccessLevel, $pageCheckEquals)
	{
		// check parameters are correct
		$error = false;

		if ($pageAccessLevel == "") {
			$pageAccessLevel = "public";
		}

		if (!isset($this->cfg->userLevels[$pageAccessLevel])) {
			// check that a valid page access level has been set
			$errorStr = "Invalid page access level";
			$error = true;
		} elseif (!is_bool($pageCheckEquals)) {
			// pageCheckEquals is of the wrong type
			$errorStr = "parameter pageCheckEquals should be of type bool";
			$error = true;
		}
		if ($error) {
			// prints an error and aborts the page output
			echo "<html><head></head><body>";
			echo $this->t("Incorrect SSP_Protection parameters") . '<br />';
			echo $this->t($errorStr);
			echo "</body></html>";
			exit();
		}
		return $pageAccessLevel;
	}

	/**
	 * removes the login info from the session record
	 */
	public function killLogin()
	{
		$values = array("UserId" => "", "SessionIp" => "", "SessionUserIp" => "", "SessionCheckIp" => "0", "SessionRandom" => "0");
		$where = array("SessionId" => session_id());
		$this->db->update($this->cfg->sessionTable, $values, $where, "SSP protect: removing session login data");
		$this->userInfo = array();
		$this->userId = "";
		$this->admin = false;
		$this->userAccessLevel = "";
		$this->userEmail = "";
		$this->userName = "";
	}

	/**
	 * Display all user data
	 */
	public function all_userdata()
	{
		$return = array();
		$return[] = $_SESSION;
		$return[] = $this->userInfo;
		$return['loggedIn'] = $this->loggedIn;
		$return['sessionId'] = session_id();
		return $return;
	}

	/**
	 * keeps a rolling record of the history
	 */
	private function pageHistory()
	{
		$currentPage = SSP_Path();
		// exit if in no history page specified for this instance
		if (count($this->config->noHistoryPages) > 0) {
			foreach ($this->config->noHistoryPages as $page) {
				if (strpos($currentPage, $page) !== false) {
					return;
				}
			}
		}

		// exit if in no history page specified
		foreach (self::$noHistoryOnPages as $page) {
			if (strpos($currentPage, $page) !== false) {
				return;
			}
		}

		// initialise SSP session save variables if not existing
		if (!isset($_SESSION["SSP_currentPage"])) {
			$_SESSION["SSP_currentPage"] = "";
			$_SESSION["SSP_previousPage"] = "";
			$_SESSION["SSP_thirdPage"] = "";
			$_SESSION["SSP_userFault"] = "";
		}

		// Get current page URL, and save last one.
		$_SESSION["SSP_thirdPage"] = $_SESSION["SSP_previousPage"];
		$_SESSION["SSP_previousPage"] = $_SESSION["SSP_currentPage"];

		// save current url to session vars.
		$_SESSION["SSP_currentPage"] = SSP_Path(true);
	}

	/**
	 * Gets latest page to return to
	 * @return string
	 */
	public function getReturnPage()
	{
		if (isset($_SESSION["SSP_currentPage"])) {
			$returnPage = $_SESSION["SSP_currentPage"];
		} else {
			$returnPage = $this->cfg->siteRoot;
		}

		return ($returnPage);
	}

	/**
	 * Check user has enough access
	 * @param string $level - allowed user level
	 * @param bool $equals - only that user type, not ones greater
	 * @return bool - true on user type allowed
	 */
	public function isAccess($level, $equals = false)
	{
		if (isset($this->cfg->userLevels[$level]) and $this->loggedIn) {
			if (($this->cfg->userLevels[$this->userAccessLevel] >= $this->cfg->userLevels[$level]) and !$equals) {
				return true;
			} elseif ($equals and ($this->userAccessLevel == $level)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Divert to login if user does not have required login level
	 * @param string $level - allowed user level
	 * @param bool $equals - only that user type, not ones greater
	 */
	public function requireAccess($level, $equals = false)
	{
		if (!$this->isAccess($level, $equals)) {
			$this->goToLogin();
		}
	}

	/**
	 * Divert to login if user not admin
	 */
	public function requireAdmin()
	{
		if (!$this->admin) {
			$this->goToLogin();
		}
	}

	/**
	 * Log a user off the system, blanks all session data except language
	 * @param Template $tpl - template object
	 * @param bool $showLogoffScreen - display the logoff screen
	 * @return string - logoff screen
	 */
	public function logoff($tpl, $showLogoffScreen = true)
	{
		$userId = $this->userId;
		$returnPage = $this->getReturnPage();
		$fields = array("SessionIp" => '', "SessionRandom" => '0', "SessionData" => '0', "SessionUserIp" => '', "UserId" => '');
		$where = array("SessionId" => $this->sessionToken);
		$this->db->update($this->cfg->sessionTable, $fields, $where, "SSP Logoff: failed to clean up user data");
		// preserve language if needed
		if ($this->cfg->translate) {
			$lang = $this->lang;
			$_SESSION = array(self::LANGSESSIONVAR => $this->lang);
		} else {
			$_SESSION = array();
		}

		if ($this->cfg->loginRememberMe and isset($_COOKIE[$this->cfg->loginRememberMeCookie])) {
			$id = $_COOKIE[$this->cfg->loginRememberMeCookie];
			// delete remember me cookie if it exists
			$options = [
				'expires' => time() - 172800,
				'path' => $this->cfg->cookiePath,
				'domain' => $this->cfg->cookieDomain,
				'secure' => $this->cfg->useSSL,
				'samesite' => 'Strict'
			];
			setcookie($this->cfg->loginRememberMeCookie, "", $options);
			$values = array("id" => $id);
			$this->db->delete($this->cfg->tableRememberMe, $values, "SSP Logoff: removing remember me entry");
		}
		$this->loggedIn = false;
		$this->userId = '';
		$this->userEmail = '';
		$this->userName = '';
		$this->userAccessLevel = '';
		$this->admin = false;
		$this->userInfo = NULL;
		$this->miscInfo = NULL;

		if ($showLogoffScreen) {
			return $this->displayLogOffScreen($tpl, $userId, $returnPage);
		}
	}

	/**
	 * Divert to the login page
	 * @param string $explanation - explanation for divert to login
	 */
	private function goToLogin($explanation = null)
	{
		$logonPath = $this->cfg->logonScript;
		$logonDivertContent = array(
			"pageTitle" => $this->t("Diverting to logon"),
			"linkText" => $this->t("Click here to login if divert does not happen within 5 seconds")
		);
		if ($explanation !== null) {
			$logonDivertContent['explanation'] = $explanation;
		}
		SSP_Divert($logonPath, $logonDivertContent, "", !$this->cfg->accessFaultDebug);
	}

	/**
	 * Totaly destroy the session
	 */
	public function sessionDestroy()
	{
		// totally destroys the session
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - 42000, '/');
		}

		// Finally, destroy the session.
		session_destroy();
	}

	/**
	 * called before protect routines check for session records stub
	 */
	public function autoLogin()
	{
	}

	/**
	 * called once protection object has logged in user data info stub
	 */
	public function succesfullLoginSessionCheck()
	{
	}

	/**
	 * Encrypt the password
	 * @param string $password - password to be encrypted
	 * @return string - encrypted password
	 */
	public function cryptPassword($password)
	{
		// Does a one way encryption of the password
		//
		// Parameters
		//  $password - supplied password string
		//
		// Returns - encrypted password

		$base64_alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
			. 'abcdefghijklmnopqrstuvwxyz0123456789';

		if ($this->cfg->encryptPassword) {
			if (defined("CRYPT_SHA512") and CRYPT_SHA512) {
				$salt = '$6$rounds=5000$';
				$saltLenght = 16;
				$generateKey = true;
			} elseif (defined("CRYPT_SHA256") and CRYPT_SHA256) {
				$salt = '$5$rounds=5000$';
				$saltLenght = 16;
				$generateKey = true;
			} elseif (defined("CRYPT_BLOWFISH") and CRYPT_BLOWFISH) {
				$salt = '$2a$07$';
				$saltLenght = 22;
				$generateKey = true;
			} else {
				$salt = "";
				$generateKey = false;
			}
			if ($generateKey) {
				$base64_alphabetLastChar = strlen($base64_alphabet) - 1;
				for ($i = 0; $i < $saltLenght; $i++) {
					$salt .= $base64_alphabet[rand(0, $base64_alphabetLastChar)];
				}
				$salt .= '$';
				$encryptedPassword = crypt(trim($password), $salt);
			} else {
				$encryptedPassword = crypt(trim($password));
			}
		} else {
			$encryptedPassword = trim($password);
		}
		return ($encryptedPassword);
	}

	/**
	 * Check for a valid password
	 * @param string $password - unencrypted password entered by user
	 * @param string $encryptedPassword - encrypted password from database
	 * @return bool - returns true on match
	 */
	public function checkPassword($password, $encryptedPassword)
	{
		$password = trim($password);
		$encryptedPassword = trim($encryptedPassword);
		if (strlen($password) < $this->cfg->minPassword) {
			return false;
		}
		if ($this->cfg->encryptPassword) {
			if (strlen($encryptedPassword) !== 0 and strlen($password) !== 0) {
				if (function_exists('hash_equals') and hash_equals($encryptedPassword, crypt($password, $encryptedPassword)) === true) {
					return true;
				} elseif (strcmp(crypt($password, $encryptedPassword), $encryptedPassword) === 0) {
					return true;
				}
			}
		} else {
			if (strcmp($encryptedPassword, $password) == 0) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Configure translation object and method
	 * @param object $translationObject
	 * @param string $translationMethod
	 */
	public static function addTranslation($translationObject, $translationMethod = 't')
	{
		self::$translate = true;
		self::$tranlator = $translationObject;
		self::$translateMethod = $translationMethod;
	}

	/**
	 * Attempt to translate a string of text
	 * @param string $text
	 * @return string
	 */
	public function t($text)
	{
		if (self::$translate) {
			$translateMethod = self::$translateMethod;
			return (self::$tranlator->$translateMethod($text));
		} else {
			return $text;
		}
	}

	/**
	 * Return if translation is enabled
	 * @return bool
	 */
	public function isTranslate()
	{
		return self::$translate;
	}

	/**
	 * Get a list of the configured languages
	 * @return array
	 */
	public function getLanguages()
	{
		return (self::$tranlator->getLangDisplay());
	}

	public static function setTemplatePath($path)
	{
		self::$templatePath = $path;
	}


	/**
	 * Log text to logging system
	 * @param string $text
	 */
	private function log($text)
	{
		if ($this->config->debug) {
			SSP_log('SSP Session debug ' . $text);
		}
	}

	public static function addNoHistoryPage($page)
	{
		self::$noHistoryOnPages[] = $page;
	}
}


/* End of file ProtectBase.php */
/* Location: ./src/ProtectBase.php */