<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	LogonBase.php
*   Created:	07/01/2005
*   Descrip:	Base class for logging into a session
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
*   Rev. Date	07/01/2005
*   Descrip:	Created.
* 
*   Revision:	b
*   Rev. Date	13/01/2016
*   Descrip:	Changed to psr-4.
*/

namespace w34u\ssp;

use w34u\ssp\sfc\Form;

abstract class LogonBase {
	// Class to handle user logons

	/**
	 * Description of the error that produce the failure
	 * @var string 
	 */
	public $errorDesc;
	/**
	 * error reported during attempted login
	 * @var bool
	 */
	public $error = false;

	/** 
	 * ssp config var
	 * @var Configuration   */
	public $cfg; // configuration
	/** 
	 * 
	 * @var SspDB database object */
	public $db; // database object
	/** 
	 *  session object
	 * @var Protect */
	public $session;
	/**
	 * Form output
	 * @var string 
	 */
	private $output = "";
	/**
	 * Session data used during authentication
	 * @var \stdClass
	 */
	private $loginSessionData = false;

	// remember me stuff
	/** 
	 * do remember me on login form
	 * @var bool  */
	private $rememberMe = false;
	/** 
	 * valid remember me user login
	 * @var bool  */
	private $rememberMeLogin = false;
	/** 
	 * request to save a cookie to remember me
	 * @var bool  */
	private $rememberMeSave = false;
	
	const FORM_LOGIN_SESSION_NAME = 'sspFormLoginData';

	/**
	 * Login base class constructor
	 * @param Protect $session - session object
	 */
	public function __construct($session){
		$this->session = $session;
		$this->cfg = Configuration::getConfiguration();
		$this->db = SspDb::getConnection();
		$this->rememberMe = $this->cfg->loginRememberMe;
		$this->loginSessionData =& SSP_attachToSeshVar(self::FORM_LOGIN_SESSION_NAME, false);
	}

	/**
	 * Display and do login using the form
	 * @param bool $ignoreToken - dont use timeout token in form
	 * @return string - display of screens
	 */
	public function do_login($ignoreToken = false){
		// if remember me enabled
		if($this->rememberMe === true){
			// if no session data see if its available
			if ($this->loginSessionData === false) {
				$this->loginSessionData = $this->rememberMeGet();
			}
			else{
				// if the user has would up back at the login form probably needs to login with another auth
				$this->loginSessionData = false;
				$this->removeRmCookie();
			}
		}
		if($this->loginSessionData === false){
			$formData = $this->processAuthForm($ignoreToken);
			// remember me check
			if($this->rememberMe and !empty($formData) and $formData->rememberMe == 1){
				$this->rememberMeSave = true;
			}
			$userData = $this->getUserData($formData);
			if ($this->cfg->loginByEmail and !empty($formData->emaillogin)){
				// commence login by email process
				$this->startEmailLogin($userData);
			}
			elseif ($userData !== false){
				if ($this->session->checkPassword($formData->password, $userData->UserPassword)) {
					$this->loginSessionData = $userData;
				} else {
					$this->errorDesc = 'Password does not match';
					// re-display login form with error
					$this->processAuthForm($ignoreToken, true, $formData);
				}
			}
			elseif($formData !== false){
				$this->errorDesc = 'Could not find user';
				$this->processAuthForm($ignoreToken, true, $formData);
			}
		}
		if($this->loginSessionData !== false){
			if($this->processTwoFactor($this->loginSessionData) === true){
				if($this->logonCheck($this->loginSessionData)){
					$this->loginSuccess();
				}
				else{
					// sort out form reload data
					$formData = new \stdClass();
					$formData->user = $this->loginSessionData->UserName;
					$formData->email = $this->loginSessionData->UserEmail;
					$this->processAuthForm($ignoreToken, true, $formData);
				}
				$this->loginSessionData = false;
			}
		}
		return $this->output;
	}

	/**
	 * Login using token from email login option
	 * @param string $token - token sent via email
	 */
	public function do_email_login($token){
		$userId = SSP_CheckResponseToken($token);
		if($userId === false){
			SSP_Divert($this->cfg->siteRoot);
		}
		$result = $this->loginUser($userId);
		if($result === false){
			SSP_Divert($this->cfg->siteRoot);
		}
		$this->loginSuccess();
	}
	
	/**
	 * Display and process login form
	 * @param bool $ignoreToken - form not to use timed token
	 * @param bool $error - error from form data processing
	 * @param \stdClass $formData - data from the form submission
	 * @return bool|\stdClass - false on failure else form data
	 */
	private function processAuthForm($ignoreToken, $error = false, $formData = null){
		// define the form to login
		$form = $this->loginScreenDefine($ignoreToken);
		// process the form on submit
		$result = $this->processForm($form, $error, $formData);
		return $result;
	}

	/**
	 * Process the remember me, and generate the errors if needed
	 * @param Form $form
	 * @param bool $error - true if problem with email of password during login
	 * @param \stdClass $formData - data from the form submission
	 * @return bool|\stdClass - false if input fails else form field values
	 */
	protected function processForm($form, $error = false, $formData = null){
		if($form->processForm($_POST)){
			if(!$form->error){
				return $form->getValues(true);
			}
			// display form on field character error
			$this->output = $form->create(true);
		}
		else{
			if(!$error) {
				$this->output = $form->create();
			}
			else{
				// login failure
				if($this->cfg->loginDebug){
					$form->tda("error", $this->errorDesc);
				}
				$firstError = "Email";
				if($this->cfg->loginType==1){
					$firstError = "User name";
				}
				$form->addError($this->session->t($firstError. " or password incorrect"));
				if($this->cfg->reLoadOnLoginFail === true and !empty($formData)){
					$form->data = get_object_vars($formData);
				}
				$this->output = $form->create(true);
			}
		}
		return false;
	}
	/**
	 * Creates the login form
	 * @param bool $ignoreToken - dont use the timeout token
	 * @return sfc\Form - form to be used to login
	 */
	protected function loginScreenDefine($ignoreToken){
		$form = new sfc\Form($this->cfg->logonScript, "noTable", "loginForm");
		$form->errorAutoFormDisplay = false;
		$form->reqChar = '%s';
		// disable checking of token for embedded login forms
		if($ignoreToken){
			$form->checkToken = false;
		}
		$form->tplf = "logonpage.tpl";

		if($this->cfg->loginType == 0){
			// login using email
			$form->fe("text", "email", "Your email");
			$form->currentElelementObject->dataType = 'email';
		}
		elseif($this->cfg->loginType==1){
			// login using username
			$form->fe("text", "user", "Your user name");
		}
		$form->currentElelementObject->autocomplete = 'off';
		$form->currentElelementObject->required = true;

		$form->fe("password", "password", "Password");
		$form->currentElelementObject->dataType = 'password';
		$form->currentElelementObject->load = false;
		if($this->cfg->loginByEmail !== true){
			$form->currentElelementObject->required = true;
		}

		// login by email option
		if($this->cfg->loginByEmail === true){
			$form->fe('check', 'emaillogin', 'Login by email', [0,1]);
			$form->currentElelementObject->textBefore = false;
			$form->currentElelementObject->encap = true;
			if($this->cfg->loginByEmailDefault === true){
				$form->currentElelementObject->deflt = 1;
			}
		}

		if($this->rememberMe){
			$form->fe("check", "rememberMe", "Remember me (do not tick this box on a public computer)", array(0,1));
			$form->currentElelementObject->textBefore = false;
			$form->currentElelementObject->encap = true;
			$form->currentElelementObject->ariaDescribedby = 'aria_remember_me_description';
		}
		if(isset($_SESSION["SSP_LoginPageAddtionalContent"]) and is_array($_SESSION["SSP_LoginPageAddtionalContent"])){
			$form = array_merge($form->tDataAdditional, $_SESSION["SSP_LoginPageAddtionalContent"]);
		}
		$form->tda("passwordRecoveryLink", $this->cfg->passwordRecover);
		$form->tda("siteHome", $this->cfg->pathSite);
		$form->tda("siteName", $this->cfg->siteName);
		$form->tda("joinSiteLink", $this->cfg->userCreation);
		return $form;
	}

	/**
	 * Generate and send email for login
	 * @param \stdClass $userInfo - user information
	 */
	private function startEmailLogin($userInfo){
		$minutes = $this->cfg->loginByEmailTimeout/60;
		$tpl = new Template(['minutes' => $minutes], 'loginbyemail.tpl');
		$this->output = $tpl->output();
		if(!empty($userInfo) and in_array($userInfo->UserAccess, $this->cfg->loginByEmailUserTypes)){
			$token = SSP_ResponseToken($userInfo->UserId, $this->cfg->loginByEmailTimeout);
			$userMisc = $this->db->get($this->cfg->userMiscTable, ['UserId' => $userInfo->UserId], 'SSP login: getting user information for email login');
			$userMisc->link = $this->cfg->loginByEmailUrl. $token;
			$userMisc->minutes = $minutes;
			$mail = new Email();
			$mail->noReplyEmail(get_object_vars($userMisc), 'emailloginbyemail.tpl', $userInfo->UserEmail, $userMisc->FirstName. ''. $userMisc->FamilyName);
		}
	}

	/**
	 * Do two factor authentication
	 * @param \stdClass $userInfo
	 * @return boolean - true on success
	 */
	protected function processTwoFactor($userInfo){
		if(!$this->cfg->twoFactorAuthentication or $userInfo->use_two_factor_auth == 0){
			// bypass if not configured or user has not configured it
			return true;
		}
		return true;
	}

	/**
	 * Divert back to page login from which login was invoked
	 */
	private function loginSuccess(){
		if($this->cfg->loginAfterGoToPage === true and !empty($this->cfg->loginAfterGoToPageUrl)){
			SSP_Divert($this->cfg->loginAfterGoToPageUrl);
		}
		$returnPage = $this->session->getReturnPage();
		if(trim($returnPage) != ""){
			// go to page that diverted to login
			$returnPath = $returnPage;
		}
		else{
			// got back to site root
			$returnPath = $this->cfg->siteRoot;
		}
		SSP_Divert($returnPath);
	}

	/**
	 * Get user data from the initial form data
	 * @param \stdClass $formData - data from login form
	 * @return object|boolean - false if no user else user login data
	 */
	private function getUserData($formData){
		if(empty($formData)){
			return false;
		}
		if($this->cfg->loginType === 0){
			// email used to get user data
			$userEmail = trim(strtolower($formData->email));
			$userInfo = $this->db->get($this->cfg->userTable, ['UserEmail' => $userEmail], "SSP Logon: Getting user login data using email");
			if(empty($userInfo)){
				$this->errorDesc = "Email not found";
			}
			return $userInfo;
		}
		elseif($this->cfg->loginType === 1){
			// user name check
			$userName = trim($formData->user);
			$userInfo = $this->db->get($this->cfg->userTable, ['UserName' => $userName], "SSP Logon: Getting user login data using username");
			if(empty($userInfo)){
				$this->errorDesc = "User name not found";
			}
			return $userInfo;
		}
		else{
			return $this->definedGetUserData($formData);
		}
	}

	/**
	 * Definable method for getting user data
	 * @param \stdClass $formData - data from login form
	 * @return object|boolean - false if no user else user login data
	 */
	protected function definedGetUserData($formData){
		return false;
	}

	/**
	 * Programmatic login using user id.
	 * @param string $userId
	 * @return boolean/string - false on fail else user id
	 */
	public function loginUser($userId){
		$userInfo = $this->db->get($this->cfg->userTable, ['UserId' => $userId], 'SSP Logon: Getting user login information for programatic login');
		if($userInfo === false){
			return false;
		}
		return $this->logonCheck($userInfo);
	}

	/**
	 * Check user account is valid for login and sets up session and remember me
	 * cookie if requested
	 * @param \stdClass $userInfo - user login information returned by the db
	 * @return string/bool - user's id on success else false
	 */
    private function logonCheck($userInfo){
        $loginOk = false;

		// if external login check ok do the rest
		if($this->userLoginCheck($userInfo)){
			// do final checks on the user and set up session info
			$userOk = true;
			
			// check user flags
			foreach($this->cfg->validUserFlags as $flagName => $validFlagValue){
				if($userInfo->$flagName != $validFlagValue){
					$userOk = false;
					$this->errorDesc = "Invalid user flag ". $flagName. " value required: ". $validFlagValue. " actual: ". $userInfo->$flagName;
					break;
				}
			}

			if($this->cfg->fixedIpAddress or $userInfo->UserIpCheck){
				// check user IP
				$allowedIpAddreses = explode(',',$userInfo->UserIp);
				$foundAddress = false;
				foreach($allowedIpAddreses as $ipAddress){
					if(strcasecmp(SSP_paddIp($_SERVER["REMOTE_ADDR"]), SSP_paddIp($ipAddress)) === 0){
						// Fixed ip correct
						// set User ip for update into the session table
						$querySet["SessionUserIp"] = $userInfo->UserIp;
						$foundAddress = true;
						break;
					}
				}
				if(!$foundAddress){
					$this->errorDesc = "Current ip address {$_SERVER["REMOTE_ADDR"]} not in users list";
					$userOk = false;
				}
			}

			// check user is not already logged in
			if($userOk and !$this->cfg->allowMultipleLogin){
				$where = array("UserId"=>$userInfo->UserId);
				if($this->db->get($this->cfg->sessionTable, $where, "SSP Logon: Checking for multiple logins")){
					// user already logged in
					$this->errorDesc = "User already logged in";
					$userOk = false;
				}
			}


			// do final set up if everything has worked ok
			if($userOk){
				$this->userSetup($userInfo);
				$loginOk = true;
			}
		}

        if($loginOk){
			// change the current session ID to prevent session fixation attacks
			$oldSessionId = session_id();
			session_regenerate_id();
			$fields = array("SessionId" => session_id());
			$where = array("SessionId" => $oldSessionId);
			$this->db->update($this->cfg->sessionTable, $fields, $where, "SSP session handling: updating new session record after session regen");
            return true;
        }
        else{
        	// long delay on login failure
            $this->error = true;
            sleep($this->cfg->logonFailDelay);
            return false;
        }
    }

	/**
	 * Set up user logged in session
	 * @param \stdClass $userInfo - user information from the database
	 */
    private function userSetup($userInfo){
	    $querySet["UserId"] = $userInfo->UserId;
	    if($this->cfg->checkIpAddress){
		    // set up IP address for this session
		    $querySet["SessionIp"] = $_SERVER["REMOTE_ADDR"];
	    }
	    if($this->cfg->randomCheck){
		    // set up random check cookie and entry
		    $randomCookie = mt_rand(0,100000);
		    $options = [
			    'expires' => 0,
			    'path' => $this->cfg->cookiePath,
			    'domain' => $this->cfg->cookieDomain,
			    'secure' => $this->cfg->useSSL,
			    'samesite' => 'Strict'
		    ];
		    setcookie($this->cfg->randomCookie, $randomCookie, $options);
		    $querySet["SessionRandom"] = $randomCookie;
	    }
	    if($this->rememberMe and !$this->rememberMeLogin and $this->rememberMeSave){
		    // create remember me cookie if the user was not logged in that way and the box was ticked
		    $idSet = SSP_uniqueId();
		    $userIdSet = $userInfo->UserId;
		    $timeSet = time()+ $this->cfg->loginRememberMePeriod * 24 * 3600;
		    $rememberMeSet = array(
			    "id" => $idSet,
			    "user_id" => $userIdSet,
			    "date_expires" => $timeSet,
		    );
		    $this->db->insert($this->cfg->tableRememberMe, $rememberMeSet, "SSP Logon: creating remember me entry");
		    $options = [
			    'expires' => $timeSet,
			    'path' => $this->cfg->cookiePath,
			    'domain' => $this->cfg->cookieDomain,
			    'secure' => $this->cfg->useSSL,
			    'samesite' => 'Strict'
		    ];
		    setcookie($this->cfg->loginRememberMeCookie, "$idSet", $options);
	    }

	    // update session table
	    $where = array("SessionId" => session_id());
	    $this->db->update($this->cfg->sessionTable, $querySet, $where, "SSP Logon: Set up user session after succesful login");

	    // update login times
	    $oldLoginTime = $userInfo->UserDateLogon;
	    $currentLogonTime = time();
	    $fields = array("UserDateLogon" => $currentLogonTime, "UserDateLastLogon" => $oldLoginTime);
	    $where = array("UserId" => $userInfo->UserId);
	    $this->db->update($this->cfg->userTable, $fields, $where, "SSP session handling: Update login times");
    }

	/**
	 * Additional user check if programmed
	 * @param object $userInfo - user login information
	 * @return bool - true on success
	 */
	public function userLoginCheck($userInfo){
		// stub for user defined login check
		return true;
	}

	/**
	 * Get remember me info
	 * @return bool|\stdClass false or user info
	 */
	private function rememberMeGet(){
		if(isset($_COOKIE[$this->cfg->loginRememberMeCookie])){
			// clean up remember me entries
			$query = "delete from {$this->cfg->tableRememberMe} where date_expires < ?";
			$values = array(time());
			$this->db->query($query, $values, "SSP Logon: cleaning up any out of date remember me entries");
			$id = $_COOKIE[$this->cfg->loginRememberMeCookie];
			// check id is in the database and still valid
			$date = time();
			$query = "select * from {$this->cfg->tableRememberMe}
			where id = ? and date_expires > ?";
			$values = array($id, $date);
			$this->db->query($query, $values, "SSP Logon: checking remeber me cookie has valid id and date");
			if($this->db->numRows()){
				$rememberInfo = $this->db->fetchRow();
				$values = $this->cfg->validUserFlags;
				$values["UserId"] = $rememberInfo->user_id;
				$userInfo = $this->db->get($this->cfg->userTable, $values, "SSP Logon: getting user info for remember me");
				$this->rememberMeLogin = true;
				return $userInfo;
			}
			else{
				$this->removeRmCookie();
			}
		}
		return false;
	}

	/**
	 * Remove remember me cookie
	 */
	private function removeRmCookie(){
		$options = [
			'expires' => time()-172800,
			'path' => $this->cfg->cookiePath,
			'domain' => $this->cfg->cookieDomain,
			'secure' => $this->cfg->useSSL,
			'samesite' => 'Strict'
		];
		setcookie($this->cfg->loginRememberMeCookie, "", $options);
	}
}
/* End of file LogonBase.php */
/* Location: ./src/LogonBase.php */