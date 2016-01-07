<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    Simple Site Protection
*   Routine:	userclasses.php
*   Created:	08/01/2005
*   Descrip:	User classes configured to modify the basic functionality.
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
*   Rev. Date	08/01/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	23/02/2011
*   Descrip:	Changed to php5 class system.
*
*   Revision:	c
*   Rev. Date	28/06/2011
*   Descrip:	Improved template handling.
*/

/**
 * SSP protection class
 *  
 */
class SSP_Protect extends SSP_ProtectBase{
	// extends the base protection classe

	/**
	 * Constructor
	 * @param string $pageAccessLevel - users allowed to access the page
	 * @param bool $pageCheckEquals - if true only this user type can access this page
	 * @param bool $doHistory - do history for this page
	 * @param SSP_ProtectConfig $config - Protected session configuration options
	 */
	public function __construct($pageAccessLevel="", $pageCheckEquals=false, $doHistory = true, $config = false){
		parent::__construct($pageAccessLevel, $pageCheckEquals, $doHistory, $config);
	}

	/**
	 * display screen shown on logging off
	 * @param SSP_template $tpl - main template
	 * @param string $userId - user id of memeber logging off
	 * @param string $returnPage - url of previous page
	 */
	public function displayLogOffScreen($tpl, $userId, $returnPage){
		// displays the logoff screen
		//
		// parameters
		// $tpl - object - main template object

        $content = array(
			"homePath" => $this->cfg->siteRoot,
			"logonPath" => $returnPage,
			"title" => "Logged off"
		);


		$logoff = new SSP_Template($content, "logoff.tpl");
		$tpl->setData("content", $logoff->output());
		echo $tpl->output();
	}
}

class SSP_Logon extends SSP_LogonBase{
    // user extensions for the logon class

	/**
	 * Login base class constructor
	 * @param SSP_Protectg $session - session object
	 * @param SSP_Template $tpl - template in which to wrap the form
	 * @param bool $ignoreToken - dont use a token on the login form
	 * @param bool $createForm - create the login form
	 */
	function __construct($session, $tpl="", $ignoreToken=false, $createForm=true){

    	if($createForm){
			parent::__construct($session, $tpl, $ignoreToken);
    	}
		else{
			$this->cfg = SSP_Configuration::get_configuration();
			$this->db = SSP_DB::get_connection();
    	}
    }

	function userLoginCheck($userInfo){
		// stub for user defined login check

		return(true);
	}

	function loginSuccessDisplay($userId, $returnPath){
		// create logon successful content

		$logonSuccessContent = array(
			"pageTitle" => "Logon Success",
			"headTitle" => $this->session->t("Welcome Back"),
			"returnPath" => $returnPath,
			"siteRoot" => $this->cfg->siteRoot
			);
		// Merge user data into page content so it can be displayed
		$where = array("UserId"=>$userId);
		$userInfo = $this->db->get($this->cfg->userTable, $where, 'Logon Success: get user data');
		$userMisc = $this->db->get($this->cfg->userMiscTable, $where, 'Logon Success: get misc user data');
		$userMiscArr = get_object_vars($userMisc);
		$userInfoArr = get_object_vars($userInfo);
		// $logonSuccessContent = array_merge($logonSuccessContent, $userMiscArr, $userInfoArr);
		// display logged in page, posibly divert to previous page.
		return($logonSuccessContent);
	}
}

class SSP_UserAdmin extends SSP_UserAdminBase{
    // class extends the user admin basic
    // for developer to sort out credit card interface, welcome screens
    // and other application specific functions

	/**
	 * Create user admin template
	 * @param array $contentMain - template content data
	 * @param bool $noMenusAndInfo - don't show user info
	 * @return SSP_Template 
	 */
	function tpl($contentMain, $noMenusAndInfo = false){
		// configure main template
		$this->ssp->pageTitleAdd("Member admin");
		if($this->id != "" and !$noMenusAndInfo){
			$name = $this->getName($this->id);
			$this->ssp->pageTitleAdd($name);
			$contentMain["displayName"] = $name;
		}
		if(isset($contentMain["title"])){
			$this->ssp->pageTitleAdd($contentMain["title"]);
		}

		// generate menu
		if($this->generateMenus and !$noMenusAndInfo){
			$menu = new menuGen();
			$path = $this->cfg->userAdminScript;
			$menu->add($path. "?command=chInfo", $this->session->t("Change Info"), ($this->command=="chInfo"));
			$menu->sv("title={$this->session->t('Change user information')}");
			$menu->add($path. "?command=chPswd", $this->session->t("Change Password"), ($this->command=="chPswd"));
			$menu->sv("title={$this->session->t('Change user password')}");
			$menu->add($path. "?command=chEmail", $this->session->t("Change Email"), ($this->command=="chEmail"));
			$menu->sv("title={$this->session->t('Change user email')}");
			$menu->add($path. "?command=info", $this->session->t("Basic Info"), ($this->command=="info"));
			$menu->sv("title={$this->session->t('Show basic information')}");
			$menu->add("", "");
			if($this->admin){
				$menu->add($path. "?command=advInfo", $this->session->t("Advanced Info"), ($this->command=="advInfo"));
				$menu->sv("title={$this->session->t('Show advanced information')}");
				$menu->add($path. "?command=chAdv", $this->session->t("Change Advanced"), ($this->command=="chAdv"));
				$menu->sv("title={$this->session->t('Change advanced information')}");
				$menu->add($path. "?command=joinEmail", $this->session->t("Send Joining Email"), ($this->command=="joinEmail"));
				$menu->sv("title={$this->session->t('Send a joinup email to the user')}");
				$menu->add($path. "?command=email", $this->session->t("Email Member"), ($this->command=="email"));
				$menu->sv("title={$this->session->t('Email the member')}");
				$menu->add("", "");
			}
			$menu->add("useradminhelp.php", $this->session->t("Help"));
			$menu->sv("target=_blank");
			$contentMain["menu"] = $menu->cMenu();
		}

		$tpl = $this->ssp->tpl($contentMain, $this->templateFile, $this->generateMenus);
		return($tpl);
	}
	
	function userJoin(){
        if($this->cfg->confirmType==0 or $this->cfg->confirmType==3){
        	$needPassword = true;
        }
        else{
        	$needPassword = false;
        }

        $form = new SFC_Form(SSP_Path(), $this->cfg->userTable, "userJoin");
        $form->tpl = $this->tpl(array("title" => "Join SSP"), true);
        if($this->subTpl != ""){
        	$form->tplf = $this->subTpl;
        }
        else{
        	$form->tplf = "userJoin.tpl";
        }
		$form->fe("text", "firstName", "First name");
		$form->fep("width=30, required=true");
		$form->fe("text", "lastName", "Last name");
		$form->fep("width=30, required=true");
        $form->fe("text", "email", "Your email");
        $form->fep("width=30,required=true, dataType=email");

        if($this->cfg->loginType == 1 or $this->cfg->getUserName){
            $form->fe("text", "name", "User name");
            $form->fep("width=15,required=true,dataType=password");
		}
		if($needPassword){
			$form->fe("password", "password", "Your password");
			$form->fep("width=15, required=true, dataType=password, minChar=". $this->cfg->minPassword);

			$form->fe("password", "password2", "Enter password again");
			$form->fep("width=15,sql=false,dataType=password,required=true");
		}
		if($this->cfg->userHasSignUpOptions){
			// user has a set of options to sign up
			$form->fe("select", "signUpLevel", "Type of membership", $this->cfg->userAccessSignUpDropdown);
			$form->fep("dataType=int, sql=false");
		}
		$form->addHidden("command", $this->command);
		$form->tda("loginPath", $this->cfg->logonScript);
		
		if($form->processForm($_POST)){
			if(!$form->error){
				$form->setField("email", strtolower($form->getField("email")));
				if($this->userCreateCheck($form)){
					echo $form->create(true);
				}
				else{
					$loginData = array();
					$userId = SSP_uniqueId();
					$loginData["UserId"] = $userId;
					$loginData["UserEmail"] = $form->getField("email");
					
					if($needPassword){
						$loginData["UserPassword"] = $this->session->cryptPassword($form->getField("password"));
					}
					if($this->cfg->userHasSignUpOptions){
						if(isset($this->cfg->userAccessSignUpLevels[$form->getField("signUpLevel")])){
							$loginData["UserAccess"] = $this->cfg->userAccessSignUpLevels[$form->getField("signUpLevel")];
						}
						else{
							$loginData["UserAccess"]=$this->cfg->userDefault;
						}
					}
					else{
						$loginData["UserAccess"]=$this->cfg->userDefault;
					}
					if($this->cfg->adminCheck){
						$loginData["UserAdminPending"]=1;
					}
					if($this->cfg->confirmType != 0){
						$loginData["UserWaiting"]=1;
					}
					if($this->cfg->furtherProgram){
						$loginData["UserPending"]=1;
					}
					// create login record
					$this->db->insert($this->cfg->userTable, $loginData, "Inserting new member login data");
					$miscData = array();
					$miscData["UserId"] = $userId;
					$miscData["FirstName"] = $form->getField("firstName");
					$miscData["FamilyName"] = $form->getField("lastName");
					$this->db->insert($this->cfg->userMiscTable, $miscData, "Inserting new member misc data");
					$this->id = $userId;
					$this->userFinish();
					$this->welcomeScreen();
				}
			}
		}
		else{
			echo $form->create();
		}
	}
	
	function getName($userId, $encode=true){
		// returns the name of the user with $userId
		//

		$fields = $this->cfg->displayNameFields;
		$format = $this->cfg->displayNameFormat;
		$where = array("UserId"=>$userId);
		if($result = $this->db->getf($this->cfg->userMiscTable, $fields, $where, "SSP Admin: Getting user name")){
			$name = vsprintf($format, get_object_vars($result));
			if($encode){
				return(htmlentities($name));
			}
			else{
				return($name);
			}
		}
		else{
			return("");
		}
	}

    function displayMisc(){
        // displays Misc user info

		$content = $this->getMisc("*", "SSP Admin: Database error on misc data fetch for display ");
		$page = new SSP_Template(get_object_vars($content), "displaymisc.tpl", false);
		$page->repFunctions["Address"] = "nl2br";
 		$contentMain["content"] = $page->output();
		$contentMain["title"] = "User information";
		$tpl = $this->tpl($contentMain);
		return $tpl->output();
   }

   /**
    * Edit Miscellaneous data
    * @param bool $creating - creating a new user
    * @param bool $reDisplay - re-display after update
    * @return string/bool - edit screen or true on save and not re-display
    */ 
   function userMisc($creating=false, $reDisplay=false){
        // Form elements
		$form= new SFC_Form(SSP_Path(), $this->cfg->userMiscTable, "userMisc");
		$form->errorAutoFormDisplay = false;
		$mainContent = array();
		if($creating){
			$mainContent["title"] = "Miscellaneous information";
		}
		else{
			$mainContent["title"] = "Edit info";
		}
        $form->tpl = $this->tpl($mainContent, $creating);
        if($this->subTpl != ""){
        	$form->tplf = $this->subTpl;
        }
        else{
        	$form->tplf = "changemisc.tpl";
        }
		$form->fe('text', "Title", "Title (Mr/Mrs/Mz/Dr/Prof.)");
		$form->fep("width=10");
		$form->fe('text', "FirstName", "First Name");
		$form->fep("width=30,required=true");
		$form->fe('text', "Initials", "Initials");
		$form->fep("width=5");
		$form->fe('text', "FamilyName","Family  Name", 30);
		$form->fep("width=30,required=true");
		$form->fe('textarea', "Address","Address");
		$form->fep("width=30,lines=5");
		$form->fe('text', "TownCity", "Town or City");
		$form->fep("width=20");
		$form->fe('text', "PostCode", "Post Code");
		$form->fep("width=10, maxLength=10, maxChar=10");
		$form->fe('text', "County", "County");
		$form->fep("width=30");
        if($creating){
		    $form->fe("submit", "submit", "Next");
        }
        else{
		    $form->fe("submit", "submit", "Save");
        }
		$form->addHidden("command", $this->command);

		$return = '';
		if($form->processForm($_POST)){
			if(!$form->error){
				// update database
				$query = $form->querySave(true);
				$where = array("UserId"=>$this->id);
				$this->db->update($this->cfg->userMiscTable, $form->saveFields, $where, "SSP user admin: Saving misc member data");
				if($reDisplay){
					$form->tda("saved");
					$return = $form->create(true);
				}
				else{
					$return = true;
				}
			}
			else{
				$return = $form->create(true);
			}
		}
		else{
			$query = $form->querySelect();
			$where = array("UserId"=>$this->id);
			$dataUpdate = $this->db->get($this->cfg->userMiscTable, $where, "SSP user admin: Getting User Misc data for update");
			$form->data = get_object_vars($dataUpdate);
			$return = $form->create(true);
		}
		return($return);
    }

    function userChangeEmail($newEmail){
        // stub for user change email function, called by SSP_UserAdmin::changeEmail
    }

    function userChangePassword($newPassword){
    	// user change password function
    	// called by SSP_UserAdmin::changePassword
    }

    function userProg($data, $userId){
		// External system interfacing
		// returns true on external setup
		//
		// parameters
		//	$data - submit data from misc data form or other useful stuff
		//	$userId - users id, used for tracking
		return(true);
	}
}

class SSP_checkData extends SSP_checkDataBase{
	
	function __construct(){
		parent::__construct();
	}
}

/* End of file userclasses.php */
/* Location: ./sspincludes/userclasses.php */