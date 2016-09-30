<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	Logon.php
*   Created:	12/01/2016
*   Descrip:	class for logging into a session
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
*   Rev. Date	12/01/2016
*   Descrip:	Class moved from a singe file with multiple classes.
*/

namespace w34u\ssp;

class Logon extends LogonBase{
	/**
	 * Login base class constructor
	 * @param w34u\ssp\Protect $session - session object
	 * @param w34u\ssp\Template $tpl - template in which to wrap the form
	 * @param bool $ignoreToken - dont use a token on the login form
	 * @param bool $createForm - create the login form
	 */
	public function __construct($session, $tpl = "", $ignoreToken = false, $createForm = true){

    	if($createForm){
			parent::__construct($session, $tpl, $ignoreToken);
    	}
		else{
			$this->cfg = Configuration::getConfiguration();
			$this->db = SspDb::getConnection();
    	}
    }

	public function userLoginCheck($userInfo){
		// stub for user defined login check

		return true ;
	}

	public function loginSuccessDisplay($userId, $returnPath){
		// create logon successful content

		$logonSuccessContent = array(
			"pageTitle" => "Logon Success",
			"headTitle" => $this->session->t("Welcome Back"),
			"returnPath" => $returnPath,
			"siteRoot" => $this->cfg->siteRoot
			);
		return $logonSuccessContent;
	}
}

/* End of file Logon.php */
/* Location: ./cfg/Logon.php */