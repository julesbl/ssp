<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	SSP_ProtectBase.php
*   Created:	07/01/2005
*   Descrip:	Base classes for used to run the SSP system.
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
*   Rev. Date	07/01/2005
*   Descrip:	Created.
*/
abstract class SSP_LogonBase {
	// Class to handle user logons

	var $errorDesc; // Description of the error that produce the failure
	var $error = false; // error reported during attempted login

	/** @var SSP_Configuration ssp config var  */
	var $cfg; // configuration
	/** @var SSP_DB database object */
	var $db; // database object
	/** @var SSP_Protect session object */
	var $session;
	/**
	 * Form output
	 * @var string 
	 */
	public $output = "";

	// remember me stuff
	/** @var bool do remeber me on login form */
	var $rememberMe = false;
	/** @var bool valid remember me user login */
	var $rememberMeLogin = false;
	/** @var bool request to save a cookie to remember me */
	var $rememberMeSave = false;


	/**
	 * Login base class constructor
	 * @param SSP_Protect $session - session object
	 * @param SSP_Template $tpl - template in which to wrap the form
	 * @param bool $ignoreToken - dont use a token on the login form
	 */
	function __construct($session, $tpl = "", $ignoreToken = false){
        
		$this->session = $session;
		$this->cfg = $this->session->cfg;
		$this->db = $this->session->db;
		$this->rememberMe = $this->cfg->loginRememberMe;

		$form = $this->loginScreenDefine($tpl, $ignoreToken);

		$this->rememberMeGet($form);

		if($this->rememberMeLogin){
			$this->loginSuccess();
		}
		if($form->processForm($_POST)){
			if(!$form->error and $this->loginFormCheck($form) and $userId = $this->logonCheck($form->userInfo)){
				$this->loginSuccess($userId);
			}
			else{
				// login failure
				if($this->cfg->loginDebug){
					$form->tda("error", $this->errorDesc);
				}
				if($this->cfg->loginType==0){
					$firstError = "Email";
				}
				if($this->cfg->loginType==1){
					$firstError = "User name";
				}
				$form->addError($this->session->t($firstError. " or password incorrect"));
				sleep($this->cfg->logonFailDelay);
				$this->output = $form->create(true);
			}
		}
		else{
			$this->output = $form->create();
		}
	}
	
	private function loginSuccess($userId = false){
		$returnPage = $this->session->getReturnPage();
		if(trim($returnPage) != ""){
			// gotto page that diverted to login
			$returnPath = $returnPage;
		}
		else{
			// got back to site root
			$returnPath = $this->cfg->siteRoot;
		}
		if($userId){
			$logonSuccessContent = $this->loginSuccessDisplay($userId, $returnPath);
		}
		else{
			$logonSuccessContent = '';
		}
		SSP_Divert($returnPath, $logonSuccessContent, "logonsuccess.tpl", $this->cfg->autoReturnAfterLogin);
	}
	
	/**
	 * Stub for login succesful display screen
	 * @param string $userId
	 * @param string $returnPath
	 */
	public function loginSuccessDisplay($userId, $returnPath){
		
	}

	/**
	 * Creates the login form
	 * @param SSP_Template $tpl - template in which to wrap the form
	 * @param bool $ignoreToken - dont use the timeout token
	 * @return SFC_Form - form to be used to login
	 */
	function loginScreenDefine($tpl, $ignoreToken){
		// defines the login form

		$useMainTemplate = is_object($tpl);

		$form = new SFC_Form($this->cfg->logonScript, "noTable", "loginForm");
		$form->errorAutoFormDisplay = false;
		// disable checking of token for embedded login forms
		if($ignoreToken){
			$form->checkToken = false;
		}
		if($useMainTemplate){
			$form->tpl = $tpl;
		}
		$form->tplf = "logonpage.tpl";

		if($this->cfg->loginType==0){
			// login using email
			$form->fe("text", "email", "Your email");
			$form->fep("required=true, dataType=email");
		}
		elseif($this->cfg->loginType==1){
			// login using username
			$form->fe("text", "user", "Your user name");
			$form->fep("required=true");
		}
		$form->fe("password", "password", "Password");
		$form->fep("required=true, dataType=password, load=false, minChar=". $this->cfg->minPassword);

		if($this->rememberMe){
			$form->fe("check", "rememberMe", "Remember me (do not tick this box on a public computer)", array(0,1));
			$form->fep("textBefore=false, encap=true");
		}
		if(isset($_SESSION["SSP_LoginPageAddtionalContent"]) and is_array($_SESSION["SSP_LoginPageAddtionalContent"])){
			$form = array_merge($form->tDataAdditional, $_SESSION["SSP_LoginPageAddtionalContent"]);
		}
		$form->tda("passwordRecoveryLink", $this->cfg->passwordRecover);
		$form->tda("siteHome", $this->cfg->pathSite);
		$form->tda("siteName", $this->cfg->siteName);
		$form->tda("joinSiteLink", $this->cfg->userCreation);
		return($form);
	}

	function loginFormCheck(&$form){
		// check the data supplied by the login form

		$passwordOk=false;

        if($this->cfg->loginType==0){
			// encrypt email and password
			$userEmail = SSP_encrypt(trim(strtolower($form->getField("email"))));
			$userPassword = trim($form->getField("password"));
			// check email and password
			$where = array();
			$where["UserEmail"] = $userEmail;
			$userInfo = $this->db->get($this->cfg->userTable, $where, "SSP Logon: Getting user login data using email");
			$this->errorDesc = "Email not found";
			if($this->db->numRows()){
				// email and password found
				$this->errorDesc = "Password not correct: '$userPassword'";
				if($this->session->checkPassword($userPassword, $userInfo->UserPassword)){
					// password the same
					$passwordOk=true;
				}
			}
		}
		elseif($this->cfg->loginType==1){
			// encrypt password
			$userName = trim($form->getField("user"));
			$userPassword = trim($form->getField("password"));
			// check user name and password
			$where = array();
			$where["UserName"] = $userName;
			$userInfo = $this->db->get($this->cfg->userTable, $where, "SSP Logon: Getting user login data using username");
			$this->errorDesc = "User name not found";
			if($this->db->numRows()){
				// user name found
				$this->errorDesc = "Password not correct: '$userPassword'";
				if($this->session->checkPassword($userPassword, $userInfo->UserPassword)){
					// password the same
					$passwordOk=true;
				}
			}
		}

		if($passwordOk){
			$form->userInfo = $userInfo;
			if($this->rememberMe and $form->getField("rememberMe") == "1"){
				$this->rememberMeSave = true;
			}
		}
		return($passwordOk);
	}

    function logonCheck($userInfo){
        // checks the return from a login form, returns true on successful
        // logon, false on failure

        // clean up old sessions
        // SSPdbgc (ini_get("session.gc_maxlifetime"));

        $loginOk=false;

		// if external login check ok do the rest
		if($this->userLoginCheck($userInfo)){
			// do final checks on the user and set up session info
			$userOk=true;
			
			// check user flags
			foreach($this->cfg->validUserFlags as $flagName => $validFlagValue){
				if($userInfo->$flagName != $validFlagValue){
					$userOk = false;
					$this->errorsDesc = "Invalid user flag ". $flagName. " value required: ". $validFlagValue. " actual: ". $userInfo->$flagName;
					break;
				}
			}

			if($this->cfg->fixedIpAddress or $userInfo->UserIpCheck){
				// check user IP
				if(SSP_paddIp($_SERVER["REMOTE_ADDR"]) == SSP_paddIp($userInfo->UserIp)){
					// Fixed ip correct
					// set User ip for update into the session table
					$querySet["SessionUserIp"]=$userInfo->UserIp;
				}
				else{
					$this->errorsDesc = "IP address not correct";
					$userOk=false;
				}
			}

			// check user is not already logged in
			if($userOk and !$this->cfg->allowMultipleLogin){
				$where = array("UserId"=>$userInfo->UserId);
				if($this->db->get($this->cfg->sessionTable, $where, "SSP Logon: Checking for multiple logins")){
					// user already logged in
					$this->errorsDesc = "User already logged in";
					$userOk = false;
				}
			}


			// do final set up if everything has worked ok
			if($userOk){
				$loginOk=true;
				$querySet["UserId"] = $userInfo->UserId;
				if($this->cfg->checkIpAddress){
					// set up IP address for this session
					$querySet["SessionIp"] = $_SERVER["REMOTE_ADDR"];
				}
				if($this->cfg->randomCheck){
					// set up random check cookie and entry
					$randomCookie=mt_rand(0,100000);
					setcookie($this->cfg->randomCookie, $randomCookie, 0, $this->cfg->cookiePath, $this->cfg->cookieDomain, $this->cfg->randomCookieSSL);
					$querySet["SessionRandom"] = $randomCookie;
				}
				if($this->rememberMe and !$this->rememberMeLogin and $this->rememberMeSave){
					// create remember me cookie if the user was not procuced and the box was ticked
					$idSet = SSP_uniqueId();
					$userIdSet = $userInfo->UserId;
					$timeSet = time()+ $this->cfg->loginRememberMePeriod * 24 * 3600;
					$rememberMeSet = array(
						"id" => $idSet,
						"user_id" => $userIdSet,
						"date_expires" => $timeSet,
					);
					$this->db->insert($this->cfg->tableRememberMe, $rememberMeSet, "SSP Logon: creating remember me entry");
					setcookie($this->cfg->loginRememberMeCookie,
							$idSet, $timeSet, "/", $this->cfg->cookieDomain, $this->cfg->randomCookieSSL);
				}

				// update session table
				$where = array("SessionId"=>session_id());
				$this->db->update($this->cfg->sessionTable, $querySet, $where, "SSP Logon: Set up user session after succesful login");

				// update login times
				$oldLoginTime = $userInfo->UserDateLogon;
				$currentLogonTime = time();

				$fields = array("UserDateLogon"=>$currentLogonTime, "UserDateLastLogon"=>$oldLoginTime);
				$where = array("UserId"=>$userInfo->UserId);
				$this->db->update($this->cfg->userTable, $fields, $where, "SSP session handling: updating new session record after session regen");
			}
		}

        if($loginOk){

			if(function_exists('session_regenerate_id')){
				// change the current session ID to prevent session fixation attacks
				// only works if php version >= 4.3.2

				$oldSessionId = session_id();

				session_regenerate_id();

				$fields = array("SessionId"=>session_id());
				$where = array("SessionId"=>$oldSessionId);
				$this->db->update($this->cfg->sessionTable, $fields, $where, "SSP session handling: updating new session record after session regen");
			}

            return($userInfo->UserId);
        }
        else{
            $this->error = true;
            sleep($this->cfg->logonFailDelay);
            return(false);
        }
    }

	function userCheck($userInfo, $formData){
		// stub for user defined login check
		return(true);
	}

	/**
	 * Get remember me info and place user info into the form if found
	 * @param SFC_Form $form form object
	 */
	function rememberMeGet(&$form){
		if($this->rememberMe){
			if(isset($_COOKIE[$this->cfg->loginRememberMeCookie])){
				// clean upremember me entries
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
					if($userInfo){
						$this->rememberMeLogin = true;
						$form->userInfo = $userInfo;
					}
				}
				else{
					$this->rememberMeLogin = false;
					// remove the cookie
					setcookie($this->cfg->loginRememberMeCookie,
							"", time()-172800, "/", $this->cfg->cookieDomain, $this->cfg->randomCookieSSL);
				}
			}
			else{
				$this->rememberMeLogin = false;
			}
		}
	}
}
/* End of file SSP_LogonBase.php */
/* Location: ./sspincludes/SSP_LogonBase.php */