<?php

/**
 *   Site by w34u
 *   http://www.w34u.com
 *   info@w34u.com
 *   +44 (0)7833 512221
 *
 *   Project:	Simple Site Protection
 *   Routine:	adminclasses.php
 *   Created:	31/01/2005
 *   Descrip:	Classes for site admin.
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
 *   Rev. Date	31/01/2005
 *   Descrip:	Created.
 *
 *   Revision:	b
 *   Rev. Date	18/09/2006
 *   Descrip:	Major update for security, hardening against sql and javascript injection attacks.
 *
 *   Revision:	c
 *   Rev. Date	19/08/2007
 *   Descrip:	template handling added and complete re-write to move away from static calls.
 *
 *   Revision:	d
 *   Rev. Date	23/02/2011
 *   Descrip:	Changed to php5 class system.
 *
 *   Revision:	e
 *   Rev. Date	28/06/2011
 *   Descrip:	Improved template handling.
 */

namespace w34u\ssp;

// User administration class
abstract class UserAdminBase {

	/**
	 * users id
	 * @var string 
	 */
	protected $id = "";

	/**
	 * session object
	 * @var Protect  */
	protected $session;

	/**
	 * SSP configuration object
	 * @var Configuration  */
	protected $cfg;

	/**
	 * database object
	 * @var SspDB */
	protected $db;

	/**
	 * SSP setup object
	 * @var Setup  */
	protected $ssp;

	/**
	 * Alternative sub template for routines
	 * @var string
	 */
	public $subTpl = "";

	/**
	 * user is admin
	 * @var bool  */
	public $admin = false;

	/**
	 * alternative template than main on
	 * @var string e */
	public $templateFile = "";

	/**
	 * generate menus for template
	 * @var bool  */
	public $generateMenus = true;

	/**
	 * Add placeholder to admin forms
	 * @var bool  */
	public $addPlaceholder;

	/**
	 * Constructor
	 * @param SSP_Protect $session - session object
	 * @param Setup $ssp
	 * @param string $id 
	 */
	public function __construct($session, $ssp, $id = "", $templateFile = "", $generateMenus = true) {
		// constructor for the user admin object

		$this->cfg = Configuration::getConfiguration();
		$this->db = SspDb::getConnection();

		if ($id != "") {
			$this->id = $id;
		} elseif (is_object($session)) {
			$this->id = $session->userId;
		}
		$this->session = $session;
		$this->ssp = $ssp;
		$this->admin = $this->session->admin;
		$this->templateFile = $templateFile;
		$this->generateMenus = $generateMenus;
	}

	/**
	 * Create a new user
	 * @return bool - returns true on success
	 */
	public function userCreate() {
		$form = new sfc\Form(SSP_Path(), $this->cfg->userTable, "userCreate");
		$form->errorAutoFormDisplay = false;
		$form->addPlaceholder = $this->addPlaceholder;
		$form->tpl = $this->tpl(array("title" => "New user"), true);
		if ($this->subTpl != "") {
			$form->tplf = $this->subTpl;
		} else {
			$form->tplf = "usercreation.tpl";
		}
		$form->fe("text", "FirstName", "First name");
		$form->fep("width=30,required=true, sql=false");

		$form->fe("text", "FamilyName", "Family name");
		$form->fep("width=30,required=true, sql=false");

		$form->fe("text", "email", "Email");
		$form->fep("width=30,required=true, dataType=email, dbField=UserEmail");

		if ($this->cfg->loginType == 1 or $this->cfg->getUserName) {
			$form->fe("text", "name", "User name");
			$form->fep("width=15,required=true,dataType=password, dbField=UserName");
		}
		$form->fe("check", "askUser", "Ask user for password (don't have to enter one)", array(0, 1));
		$form->fep('sql=false');

		$form->fe("password", "password", "Password");
		$form->fep("width=15, dataType=password, dbField=UserPassword");

		$form->fe("password", "password2", "Enter password again");
		$form->fep("width=15,sql=false,dataType=password");
		if ($this->cfg->fixedIpAddress) {
			$form->fe("text", "ip", "IP address");
			$form->fep("width=35,dataType=real, dbField=UserIp");
		}

		// allow flags to be set by admin
		$form->fe("select", "UserAccess", "User Access rights", $this->cfg->userAccessTypeDropdown);
		$form->fep("dataType=password");
		$checkData = array("0", "1");
		$fep = "dataType=bin";
		$form->fe("check", "UserDisabled", "User Disabled", $checkData);
		$form->fep($fep);
		$form->fe("check", "UserPending", "User Pending program enable", $checkData);
		$form->fep($fep);
		$form->fe("check", "UserAdminPending", "User waiting admin vetting", $checkData);
		$form->fep($fep);
		$form->fe("check", "CreationFinished", "User creation finished", $checkData);
		$form->fep($fep . ",deflt=true");
		$form->fe("check", "UserWaiting", "Waiting for user to act on email", $checkData);
		$form->fep($fep);

		$form->fe("submit", "submit", "Create user");

		// Check for form submission
		$result = '';
		if ($form->processForm($_POST)) {
			if (!$form->error) {
				$form->setField("email", strtolower($form->getField("email")));
				if ($this->userCreateCheck($form)) {
					$result = $form->create(true);
				} else {
					// create user
					// user ID
					$userId = SSP_uniqueId();
					$add["UserId"] = $userId;
					$add["UserDateCreated"] = time();

					if ($form->getField('askUser') == 0) {
						$form->setField("password", $this->session->cryptPassword($form->getField("password")));
					}

					$form->alsoAdd = $add;
					$query = $form->querySave();
					$this->db->insert($this->cfg->userTable, $form->saveFields, "SSP Admin User Creation: Creating new user ");
					$data = array(
						'UserId' => $userId,
						'FirstName' => $form->getField('FirstName'),
						'FamilyName' => $form->getField('FamilyName'),
					);
					$this->db->insert($this->cfg->userMiscTable, $data, "SSP Admin User Creation: Creating new user misc data");
					if ($form->getField('askUser') != 0) {
						// send email if new user entering password
						$this->userJoinEmail($userId);
					}
					$result = true;
				}
			} else {
				$result = $form->create(true);
			}
		} else {
			$result = $form->create();
		}
		return($result);
	}

	/**
	 * Check user information supplied for creation, e.g. duplicate emails, usernames, passwords not the same
	 * @param sfc\Form $form - user creation form
	 * @return bool - returns true on failure 
	 */
	protected function userCreateCheck(&$form) {
		// Checks the user creation form for the passwords being the same

		$error = false;
		if (isset($form->elements["password"])) {
			if ($form->getField('askUser') === '0' and strlen(trim($form->getField("password"))) < $this->cfg->minPassword) {
				$form->setError("password", "Please enter a password at least {$this->cfg->minPassword} in length");
				$error = true;
			} elseif ($form->getField('askUser') === '0' and strcmp($form->getField("password"), $form->getField("password2")) != 0) {
				// check passwords are identical if requested
				$form->setError("password2", "The two passwords must be identical");
				$error = true;
			}
		}

		// encrypt email
		$email = SSP_encrypt($form->getField("email"), $this->cfg->useEncryption);
		// check email is unique
		$values["UserEmail"] = $form->getField("email");
		if ($this->db->get($this->cfg->userTable, $values, "SSP User Creation: Checking user email unique")) {
			// flag duplicate email
			$form->setError("email", "Email needs to be unique");
			$error = true;
		} else {
			$form->setField("email", $email);
		}
		// check user name is unique
		if (isset($form->elements["name"])) {
			$values = array(); // clear array
			$values["UserName"] = $form->getField("name");
			if ($this->db->get($this->cfg->userTable, $values, "SSP User Creation: Checking user name is unique")) {
				// flag duplicate user name
				$form->setError("name", "User name needs to be unique");
				$error = true;
			}
		}
		return($error);
	}

	/**
	 * Creates an admin user if non exists
	 * @param array $content - Content from previous routines
	 * @return bool - true on success
	 */
	public function adminCreate($content) {
		// check for any exisiting admin users
		$adminAccess = array_filter($this->cfg->userLevels, function($level) {
			$cfg = \w34u\ssp\Configuration::getConfiguration();
			if ($level >= $cfg->adminLevel) {
				return true;
			}
			return false;
		});
		$sql = '
			select
				UserId
			from
				%s
			where
				UserAccess in (%s)
				 ';
		$sql = sprintf($sql, $this->cfg->userTable, "'" . implode("','", array_keys($adminAccess)) . "'");
		$this->db->query($sql, [], "SSP Admin Creation: Finding any admin users");
		if ($this->db->numRows() > 0) {
			$content['admin_creation_status'] = $this->session->t('There are already admin users in the system, please delete these first if attempting to recover access to the system.');
		} else {
			// create admin user
			$form = new sfc\Form(SSP_Path(), 'none', 'createAdminForm');
			$form->tplf = 'adminCreateForm.tpl';
			$form->errorAutoFormDisplay = false;
			if ($this->cfg->loginType === 0) {
				$form->fe('text', 'email', 'Admin email');
				$form->currentElelementObject->required = true;
				$form->currentElelementObject->dataType = 'email';
			}
			if ($this->cfg->loginType === 1) {
				$form->fe('text', 'userName', 'Admin user name');
				$form->currentElelementObject->required = true;
				$form->currentElelementObject->dataType = 'email';
			}
			$form->fe('text', 'password1', 'Password');
			$form->currentElelementObject->required = true;
			$form->currentElelementObject->dataType = 'password';
			$form->fe('text', 'password2', 'Repeat the password');
			$form->currentElelementObject->required = true;
			$form->currentElelementObject->dataType = 'password';
			if ($form->processForm($_POST)) {
				if (!$form->error) {
					if (strcmp($form->getField('password1'), $form->getField('password2')) === 0) {
						$userId = md5(uniqid($this->cfg->magicUser, true));
						$userPassword = $this->session->cryptPassword($form->getField('password1'));
						$userDate = time();
						$fields = array(
							"UserId" => $userId,
							"UserPassword" => $userPassword,
							"UserAccess" => 'admin',
							"UserDateCreated" => $userDate,
							"CreationFinished" => "1",
						);
						if ($this->cfg->loginType === 0) {
							$fields['UserEmail'] = $form->getField('email');
						}
						if ($this->cfg->loginType === 1) {
							$fields['UserName'] = $form->getField('userName');
						}
						$this->db->insert($this->cfg->userTable, $fields, "SSP Admin Creation: Creating admin entry");

						// create empty misc info
						$this->userMiscInit($userId);
						$userInfo = $this->db->get($this->cfg->userTable, array("UserId" => $userId), "Getting user info for auto login of admin on creation");
						$login = new Logon($this->session, "", true, false);
						$login->userLoginCheck($userInfo);
						$content['admin_creation_status'] = $this->session->t('Admin user created');
					} else {
						$form->setError('password1', 'Please check the passwords, they must be the same');
						$content['form'] = $form->create(true);
					}
				} else {
					$content['form'] = $form->create(true);
				}
			} else {
				$content['form'] = $form->create();
			}
		}
		$content['adminPath'] = $this->cfg->totalAdminScript;
		$tpl = new Template($content, 'adminCreate.tpl');
		$mainTpl = $this->tpl(['content' => $tpl->output(), 'title' => 'Site database creation and intialisation'], true);
		return $mainTpl->output();
	}

	/**
	 * Initialises the user misc table for a user
	 * @param string $userId - user id
	 */
	private function userMiscInit($userId) {
		$this->id = $userId;

		$values["UserId"] = $userId;
		$this->db->insert($this->cfg->userMiscTable, $values, "User Creation: User Misc init failure");
	}

	/**
	 * Send a joinup email to the current user
	 * @param string $id User id
	 */
	public function userJoinEmail($id) {
		// get user information
		$fields = array("UserEmail");
		$emailAddress = $this->getUser($fields, "SSP Admin: Getting email for user joining email", $id);
		$userData = $this->getMisc("*", "SSP Admin: Getting user misc data for joining email", $id);
		$content = array();
		$content = array_merge($content, get_object_vars($userData));
		if ($this->cfg->confirmType == 2 or $this->cfg->confirmType == 3) {
			// add joining link if confirmation needed
			$content["joinLink"] = $this->cfg->userConfirm;
			$token = SSP_ResponseToken($id, $this->cfg->confirmExpiry);
			$content['token'] = $token;
			$this->updateUser(array("UserWaiting" => 1), "SSP Admin: Seeting user waiting flag on joiup email creation", $id);
		}
		// send email
		$email = new Email();
		$result = $email->noReplyEmail($content, "emailmemberjoining.tpl", $emailAddress->UserEmail, $userData->FirstName . ' ' . $userData->FamilyName);
		if ($result === false) {
			SSP_error('SSP Admin: failed to send join email to user ' . $emailAddress->UserEmail, E_USER_ERROR);
		}
	}

	/**
	 * Display screen to ask if a joinup email is required
	 * @return bool - true on success
	 */
	public function sendJoinupEmail() {
		$form = new sfc\Form(SSP_Path(), "noTable", "joinUpEmail");
		$form->tpl = $this->tpl(array("title" => "Send joining email"));
		$form->tplf = "sendjoinupemail.tpl";
		$form->errorAutoFormDisplay = false;
		$form->fe("submit", "submit", "Send joinup email to this user?");

		if ($form->processForm($_POST)) {
			if (!$form->error) {
				$this->userJoinEmail($this->id);
				$form->tda("saved");
				return $form->create();
			} else {
				return $form->create(true);
			}
		} else {
			return $form->create();
		}
	}

	/**
	 * Finishes off user configuration after user joins
	 * @param string $userId - new users id
	 */
	protected function userFinish($userId) {
		// if admin has to check the user send an email to admin to inform them of a new one
		if ($this->cfg->adminCheck) {
			// send email
			$email = new Email();
			$content = [
				'viewLink' => $this->cfg->userAdminScript . '/info/' . $userId,
			];
			$result = $email->noReplyEmail($content, "emailadminmemberjoined.tpl", $this->cfg->adminEmail, $this->cfg->adminName);
			if ($result === false) {
				SSP_error('SSP Admin: failed to admin ' . $this->cfg->adminEmail, E_USER_ERROR);
			}
			return;
		}
		// Send joining email
		$this->userJoinEmail($userId);
	}
	
	public function userAdminOk(){
		$user = $this->getUser('', 'SSP Admin, getting user flags for admin enable user');
		if($user->UserAdminPending !== '0'){
			$update = ['UserAdminPending' => '0'];
			$this->updateUser($update, 'SSP Admin, Clearing admin pending flag');
			$this->userJoinEmail($this->id);
			$tpl = new Template([], 'userAdminEnabled.tpl');
			$content = ['title' => 'User enabled by admin', 'content' => $tpl->output()];
			return($this->tpl($content)->output());
		}
	}

	/**
	 * display the welcome screen on successful user join
	 */
	public function welcomeScreen() {
		// creates the user welcome screen after creation

		$content = array("rootPath" => $this->cfg->siteRoot);
		$userData = $this->getMisc("*", "Getting welcome screen user data");
		$content = array_merge($content, get_object_vars($userData));
		$page = new Template($content, "welcomescreen.tpl");
		$tpl = $this->tpl(array("content" => $page->output()));
		return $tpl->output();
	}

	/**
	 * Finish user creation when user replies to email
	 * @param string $token - returned token
	 * @param array $userContent - more content for the template
	 */
	public function userConfirm($token, $userContent = array()) {
		// set up saving id for password setting
		if (!isset($_SESSION['SSP_user_confirm_id'])) {
			$_SESSION['SSP_user_confirm_id'] = '';
		}
		$id = & $_SESSION['SSP_user_confirm_id'];
		if ($id === '') {
			$id = SSP_CheckResponseToken($token);
		}
		if ($id !== false) {
			$values = array("UserId" => $id, "UserWaiting" => "1");
			if ($this->db->get($this->cfg->userTable, $values, "SSP user creation: checking for user entry needing email return")) {
				$success = '';
				if ($this->cfg->confirmType == 2) {
					// prompt for password
					$success = $this->changePassword($id, false);
				} else {
					$success = true;
				}

				if ($success === true) {
					unset($_SESSION['SSP_user_confirm_id']);
					$values = array("UserWaiting" => "0");
					$this->updateUser($values, "SSP user creation: updating user entry to reflect reply to email", $id);
					// display joined screen
					$content = array("rootPath" => $this->cfg->siteRoot);
					$content = array_merge($content, get_object_vars($this->getMisc("*", "SSP user creation: Getting user misc info", $id)), $userContent);
					$page = new Template($content, "usercreationconfirm.tpl");
					$contentMain = array("content" => $page->output());
					$tpl = $this->tpl($contentMain, true);
					return $tpl->output();
				} else {
					return $success;
				}
			} else {
				unset($_SESSION['SSP_user_confirm_id']);
				return $this->response("User Confirm Failure<br />Invalid User");
			}
		} else {
			unset($_SESSION['SSP_user_confirm_id']);
			return $this->response("User Confirm Failure<br />Invalid token");
		}
	}

	/**
	 * Change password routine
	 * @param string $userId - users id
	 * @param bool $requirePassword - prompt for the orriginal password
	 * @param bool $reDisplay - re-display form on success
	 * @return bool - true on success else returns the form
	 */
	public function changePassword($userId = "", $requirePassword = true, $reDisplay = false) {
		if ($userId != "") {
			$id = $userId;
		} else {
			$id = $this->id;
		}

		$form = new sfc\Form(SSP_Path(), $this->cfg->userTable, "changePassword");
		$form->errorAutoFormDisplay = false;
		$form->addPlaceholder = $this->addPlaceholder;
		$form->tpl = $this->tpl(array("title" => "Change password"));
		if ($this->subTpl != "") {
			$form->tplf = $this->subTpl;
		} else {
			$form->tplf = "changepassword.tpl";
		}
		$form->userId = $id;
		if ($requirePassword) {
			// ask for orriginal password
			$form->fe("password", "oldPassword", "Your orriginal Password");
			$form->fep("required=true,width=30,load=false,sql=false,dataType=password");
		}

		$form->fe("password", "password", "New password");
		$form->fep("required=true,width=30,load=false,dataType=password, dbField=UserPassword, minChar=" . $this->cfg->minPassword);

		$form->fe("password", "password2", "Enter new password again");
		$form->fep("required=true,width=30,load=false,sql=false,dataType=password, minChar=" . $this->cfg->minPassword);


		$return = '';

		if ($form->processForm($_POST)) {
			if (!$form->error) {

				if ($this->changePasswordCheck($form)) {
					$return = $form->create(true);
				} else {
					// call additional routine, may send email or whatever
					$this->userChangePassword($form->getField("password"));

					// update database
					$form->setField("password", $this->session->cryptPassword($form->getField("password")));
					$form->querySave();
					$where = array("UserID" => $id);
					$this->db->update($this->cfg->userTable, $form->saveFields, $where, "SSP Admin: Saving new password");
					if ($reDisplay) {
						$form->tda("saved");
						$return = $form->create(false);
					} else {
						$return = true;
					}
				}
			} else {
				$return = $form->create(true);
			}
		} else {
			$return = $form->create();
		}

		return($return);
	}

	/**
	 * Check both the change password form
	 * @param sfc\Form $form - form object to check
	 * @return bool - true on error
	 */
	private function changePasswordCheck(&$form) {
		$error = false;
		if (strcmp($form->getField("password"), $form->getField("password2")) != 0) {
			$form->setError("password", "Both passwords must be the same");
			$error = true;
		}
		if (isset($form->elements["oldPassword"])) {
			$fields = array("UserPassword");
			$result = $this->getUser($fields, "Get user password failed", $form->userId);
			$passwordOK = $this->session->checkPassword($form->getField("oldPassword"), $result->UserPassword);
			if (!$passwordOK) {
				// no result returned for the password entered
				$form->setError("oldPassword", "Invalid orriginal password");
				$error = true;
			}
		}
		return($error);
	}

	/**
	 * Change email with form
	 * @param bool $requirePassword - prompts for password on true
	 * @param bool $reDisplay - re-display form on success
	 * @return bool/string - true on success else outputs the form
	 */
	public function changeEmail($requirePassword, $reDisplay = false) {
		$form = new sfc\Form(SSP_Path(), $this->cfg->userTable, "changeEmail");
		$form->errorAutoFormDisplay = false;
		$form->addPlaceholder = $this->addPlaceholder;
		$form->tpl = $this->tpl(array("title" => "Change email"));
		if ($this->subTpl != "") {
			$form->tplf = $this->subTpl;
		} else {
			$form->tplf = "changeemail.tpl";
		}
		$form->userId = $this->id;
		if ($requirePassword) {
			// ask for orriginal password
			$form->fe("password", "password", "Your password");
			$form->fep("required=true,width=15,load=false,sql=false,dataType=password");
		}

		$form->fe("text", "email", "New email");
		$form->fep("required=true,width=30,dataType=email, dbField=UserEmail");

		$form->fe("submit", "submit", "Save new email");

		$return = '';
		if ($form->processForm($_POST)) {
			if (!$form->error) {
				if ($this->changeEmailCheck($form)) {
					$return = $form->create(true);
				} else {
					// update database
					$this->userChangeEmail($form->elements["email"]->field);
					$form->elements["email"]->field = SSP_Encrypt($form->elements["email"]->field, $this->cfg->useEncryption);
					$form->querySave();
					$this->updateUser($form->saveFields, "SSP Admin: Saving new email");
					if ($reDisplay) {
						$form->tda("saved");
						$return = $form->create();
					} else {
						$return = true;
					}
				}
			} else {
				$return = $form->create(true);
			}
		} else {
			$return = $form->create();
		}
		return($return);
	}

	/**
	 * Check email form
	 * @param sfc\Form $form - form object
	 * @return bool - true on success
	 */
	private function changeEmailCheck(&$form) {
		$error = false;
		if (isset($form->elements["password"])) {
			$fields = array("UserPassword");
			$result = $this->getUser($fields, "SSP Admin: Get user password failed", $form->userId);
			if (!$this->session->checkPassword($form->getField("password"), $result->UserPassword)) {
				// no result returned for the password entered
				$form->setError("password", "Invalid password");
				$error = true;
			}
		}

		// check for duplicate emails
		if (!$this->cfg->allowDuplicateEmails) {
			$values = array("UserEmail" => SSP_Encrypt($form->getField("email"), $this->cfg->useEncryption));
			$this->db->get($this->cfg->userTable, $values, "SSP Admin, change email: checking for duplicate email");
			if ($this->db->numRows()) {
				$form->setError("email", "Email is already in use.");
				$error = true;
			}
		}
		return($error);
	}

	/**
	 * Change advanced admin configuration
	 * @return bool - true on success
	 */
	public function changeAdmin() {
		$checkData = array('0', '1');
		$form = new sfc\Form(SSP_Path(), $this->cfg->userTable, "changeAdmin");
		$form->tpl = $this->tpl(array("title" => "Change advanced information"));
		$form->addPlaceholder = $this->addPlaceholder;
		$form->tplf = "changeadmin.tpl";
		$form->fe("check", "UserIpCheck", "Check user IP for logon and session", $checkData);
		$form->fe("text", "UserIp", "User IP address", "Users Ip address");
		$form->fep("maxChar=255, maxLength=255");
		$form->fe("check", "use_two_factor_auth", "Enable two factor authentication", $checkData);
		$form->fe("select", "UserAccess", "User Access rights", $this->cfg->userAccessTypeDropdown);
		$form->fe("check", "UserDisabled", "User Disabled", $checkData);
		$form->fe("check", "UserPending", "User Pending program enable", $checkData);
		$form->fe("check", "UserAdminPending", "User waiting admin vetting", $checkData);
		$form->fe("check", "CreationFinished", "User creation finished", $checkData);
		$form->fe("check", "UserWaiting", "Waiting for user to act on email", $checkData);

		$result = "";
		if ($form->processForm($_POST)) {
			if (!$form->error) {
				// Submit changed data
				$query = $form->querySave();
				$this->updateUser($form->saveFields, "SSP Admin: Updating user advanced information");
				$form->tda("saved");
				$result = $form->create(true);
			}
		} else {
			// get old data
			$form->querySelect();
			$form->data = get_object_vars($this->getUser($form->selectFields, "SSP Admin: Getting advanced user data for change"));
			$result = $form->create(true);
		}
		return($result);
	}

	/**
	 * Display admin information
	 */
	public function displayAdminInfo() {
		// Displays admin flags and information on a user

		$info = get_object_vars($this->getUser("*", "Getting admin data for display"));
		$info["userAccess"] = $this->session->t($this->cfg->userAccessTypeDropdown[$info["UserAccess"]]);

		$info["UserEmail"] = SSP_Decrypt($info["UserEmail"], $this->cfg->useEncryption);
		if ($info["UserIpCheck"]) {
			$info["ipCheckEnabled"] = "";
		} else {
			$info["ipCheckDisabled"] = "";
		}
		$info["UserDateLogon"] = date("r", $info["UserDateLogon"]);
		$info["UserDateLastLogon"] = date("r", $info["UserDateLastLogon"]);
		$info["UserDateCreated"] = date("r", $info["UserDateCreated"]);

		if ($info["use_two_factor_auth"] != 0) {
			$info["Use_two_factor_auth"] = "";
		}

		if ($info["UserDisabled"]) {
			$info["userDisabled"] = "";
		}

		if ($info["UserPending"]) {
			$info["userPending"] = "";
		}

		if ($info["UserAdminPending"]) {
			$info["userAdminPending"] = "";
		}

		if ($info["CreationFinished"]) {
			$info["creationFinished"] = "";
		}

		if ($info["UserWaiting"]) {
			$info["userWaiting"] = "";
		}

		$page = new Template($info, "displayAdminInfo.tpl", false);
		$mainContent = array();
		$mainContent["title"] = "Advanced information";
		$mainContent["content"] = $page->output();
		$tpl = $this->tpl($mainContent);
		return($tpl->output());
	}

	/**
	 * Send and email to the user
	 * @param string $userIdTo - to users id
	 * @param string $userIdFrom - user from id
	 * @return string - form output
	 */
	public function emailUser($userIdTo, $userIdFrom) {
		$form = new sfc\Form(SSP_Path(), "noTable", "emailUser");
		$form->tpl = $this->tpl(array("title" => "Email member"));
		$form->tplf = "sendemailtomember.tpl";
		$form->errorAutoFormDisplay = false;
		$form->fe("text", "subject", "Subject");
		$form->fep("required=true");
		$form->fe("textarea", "message", "Message");
		$form->fep("required=true, width=40, lines=10");
		$form->fe("submit", "submit", "Send Email");
		if ($form->processForm($_POST)) {
			if (!$form->error) {
				// get to email
				$query = sprintf("select u.%s, m.%s, m.%s from %s as u, %s as m where u.%s = ? and m.%s = u.%s", $this->db->qt("UserEmail"), $this->db->qt("FamilyName"), $this->db->qt("FirstName"), $this->cfg->userTable, $this->cfg->userMiscTable, $this->db->qt("UserId"), $this->db->qt("UserId"), $this->db->qt("UserId")
				);
				$values = array($userIdTo);
				$this->db->query($query, $values, "SSP Admin send email: Getting to email and name");

				$rowTo = $this->db->fetchRow();
				$emailTo = SSP_Decrypt($rowTo->UserEmail, $this->cfg->useEncryption);

				// get from information
				$where = array("UserId" => $userIdFrom);
				$rowFrom = $this->db->get($this->cfg->userMiscTable, $where, "SSP Admin send email: Getting from name");

				// build email
				$content["message"] = $form->getField("message");
				$content["messageHtml"] = nl2br($form->getField("message"));
				$content["subject"] = $form->getField("subject");
				$content["firstName"] = $rowFrom->FirstName;
				$content["familyName"] = $rowFrom->FamilyName;
				$email = new Email();
				$result = $email->generalEmail($content, "emailmember.tpl", $this->session->userEmail, ($rowFrom->FirstName . " " . $rowFrom->FamilyName), $emailTo, ($rowTo->FirstName . " " . $rowTo->FamilyName));
				if ($result === false) {
					SSP_error('SSP Admin: failed to send email to user ' . $emailTo, E_USER_ERROR);
				}
				$form->tda("saved");
				return $form->create(true);
			} else {
				return $form->create(true);
			}
		} else {
			return $form->create();
		}
	}

	/**
	 * Start recovery of a users password
	 */
	public function startPasswordRecovery() {
		$form = new sfc\Form(SSP_Path(), "noTable", "startPasswordRecovery");
		$form->tplf = "passwordrecover.tpl";
		$form->tpl = $this->tpl(array("title" => "Password recovery"));
		$form->errorAutoFormDisplay = false;
		$form->tda("loginPath", $this->cfg->logonScript);
		$form->fe("text", "email", "Enter your registered email");
		$form->fep("required=true,width=30, dataType=email");
		$form->currentElelementObject->autocomplete = 'off';
		$form->fe("submit", "submit", "Recover Password");
		$form->fep("elClass=SSPFormButton");

		if ($form->processForm($_POST)) {
			if (!$form->error) {
				// check for the email
				$fields = array("UserId", "UserEmail", "UserName", "UserPassword");
				$where["UserEmail"] = SSP_encrypt(trim($form->getField("email")), $this->cfg->useEncryption);
				$row = $this->db->getf($this->cfg->userTable, $fields, $where, "SSP user admin: getting user info for password recovery");
				if ($this->db->numRows()) {
					// found the email
					$rowMisc = $this->db->get($this->cfg->userMiscTable, array("UserId" => $row->UserId), "Getting user name for password recovery");
					if ($this->cfg->passwordRecovery == 0 or $this->cfg->encryptPassword) {
						// use user change of password method
						// Generate user response token
						$token = SSP_ResponseToken($row->UserId, $this->cfg->recoverTime);

						// generate email
						if ($this->cfg->loginType == 1) {
							// Supply user name if used for login
							$content["UserName"] = $row["UserName"];
						}
						$content["link"] = $this->cfg->newPassword;
						$content['token'] = $token;
						$content["adminEmail"] = $this->cfg->adminEmail;

						$email = new Email();
						$email->noReplyEmail($content, "emailpasswordrecovery0.tpl", $row->UserEmail, $rowMisc->FirstName . " " . $rowMisc->FamilyName);
					} else {
						// email all info to the user
						// generate email
						if ($this->cfg->loginType == 1) {
							// Supply user name if used for login
							$content["UserName"] = $row["UserName"];
						}
						$content["UserPassword"] = $row["UserPassword"];
						$content["adminEmail"] = $this->cfg->adminEmail;

						$email = new Email();
						$email->noReplyEmail($content, "emailpasswordrecovery1.tpl", $row->UserEmail, $rowMisc->FirstName . " " . $rowMisc->FamilyName);
					}
					$form->tda("sent");
					$result = $form->create();
				} else {
					// email not found, pretend its going anyway to prevent enumeration by failure
					$form->tda("sent");
					$result = $form->create();
				}
			} else {
				$result = $form->create(true);
			}
		} else {
			// display form
			$result = $form->create();
		}
		return $result;
	}

// end function - startPasswordRecovery

	/**
	 * Finish password recovery
	 * @param string $token - password recovery token
	 * @return string - content to be displayed
	 */
	public function finishPasswordRecovery($token) {
		// check for new password process in operation
		if (!isset($_SESSION["SSP_getPassword"])) {
			$_SESSION["SSP_getPassword"] = false;
			$_SESSION["SSP_getPassworduserId"] = "";
		}
		$getPassword = & $_SESSION["SSP_getPassword"];
		$userId = & $_SESSION["SSP_getPassworduserId"];

		// Check for recovery token
		if ($token or $getPassword) {
			if ($getPassword or $userId = SSP_CheckResponseToken($token)) {
				// valid token found, ask for new password
				$getPassword = true;
				$result = $this->changePassword($userId, false, false);
				if ($result === true) {
					// Password change succesfull
					unset($_SESSION["SSP_getPassword"], $_SESSION["SSP_getPassworduserId"]);
					$tpl = new Template(['home_link' => $this->cfg->siteRoot], 'passwordrecoverycomplete.tpl');
					$tplm = $this->tpl(['content' => $tpl->output()], true);
					return $tplm->output();
				} else {
					return $result;
				}
			} else {
				// Token has come back as invalid
				return $this->response("Invalid recovery link, please check you have used the correct recovery email, if problems persist please contact site admin");
			}
		} else {
			// No token
			return $this->response("Invalid recovery link, please check the email supplied and ensure you enter the whole of the url supplied, if problems persist please contact site admin");
		}
	}

	/**
	 * Updates fields in the user table to the required value
	 * @param array $fields - fields and values to be updated
	 * @param string $errorString - error string used if it fails
	 * @param string $id - user id if different from internal
	 * @return bool
	 */
	protected function updateUser(array $fields, $errorString, $id = "") {
		if ($id == "") {
			$where["UserId"] = $this->id;
		} else {
			$where["UserId"] = $id;
		}
		$this->db->update($this->cfg->userTable, $fields, $where, $errorString);
		return(true);
	}

	/**
	 * Gets the specifeid fields from the Main logon table
	 * @param array $fields - fields to get, null gets the lot
	 * @param string $errorString - erro string on failure
	 * @param string $id - user id, optional
	 * @return object
	 */
	protected function getUser($fields, $errorString, $id = "") {
		if ($id == "") {
			$where["UserId"] = $this->id;
		} else {
			$where["UserId"] = $id;
		}
		if (is_Array($fields)) {
			$row = $this->db->getf($this->cfg->userTable, $fields, $where, $errorString);
		} else {
			$row = $this->db->get($this->cfg->userTable, $where, $errorString);
		}
		return($row);
	}

	/**
	 * Updates fields in the miscellaneous table to the required value
	 * @param array $fields - fields and values
	 * @param string $errorString - error string for failure
	 * @param string $id - optiohal user id
	 * @return bool - true on success
	 */
	protected function updateMisc(array $fields, $errorString, $id = "") {
		if ($id == "") {
			$where["UserId"] = $this->id;
		} else {
			$where["UserId"] = $id;
		}
		$useDb->update($this->cfg->userMiscTable, $fields, $where, $errorString);
		return(true);
	}

	/**
	 * Gets the specifeid fields from the misc table
	 * @param array $fields - fields to get, null gets the lot
	 * @param string $errorString - error to generate on failure
	 * @param string $id - optinoal user id
	 * @return object
	 */
	protected function getMisc($fields, $errorString, $id = "") {
		if ($id == "") {
			$where["UserId"] = $this->id;
		} else {
			$where["UserId"] = $id;
		}
		if (is_Array($fields)) {
			$row = $this->db->getf($this->cfg->userMiscTable, $fields, $where, $errorString);
		} else {
			$row = $this->db->get($this->cfg->userMiscTable, $where, $errorString);
		}
		return($row);
	}

	/**
	 * Display a response in the main template
	 * @param string $text - text to show
	 * @return string - total output including main template output
	 */
	protected function response($text) {
		$contentMain["content"] = "<br /><br /><p>{$this->session->t($text)}</p>";
		$tpl = $this->tpl($contentMain);
		$tpl->ne('content');
		return $tpl->output();
	}

	/**
	 * Create user admin template - stub
	 * @param array $contentMain - template content data
	 * @param bool $noMenusAndInfo - don't show user info
	 * @return Template 
	 */
	function tpl($contentMain, $noMenusAndInfo = false) {
		
	}

}

/* End of file adminclasses.php */
/* Location: ./sspincludes/adminclasses.php */