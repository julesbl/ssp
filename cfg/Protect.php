<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	Protect.php
*   Created:	12/01/2016
*   Descrip:	SSP protection class, instanciated at the top of protected pages.
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
*   Descrip:	Class moved from a singe file wiht multiple classes.
*/

namespace w34u\ssp;

class Protect extends ProtectBase{
	/**
	 * Constructor
	 * @param string $pageAccessLevel - users allowed to access the page
	 * @param bool $pageCheckEquals - if true only this user type can access this page
	 * @param bool $doHistory - do history for this page
	 * @param ProtectConfig $config - Protected session configuration options
	 */
	public function __construct($pageAccessLevel="", $pageCheckEquals=false, $doHistory = true, $config = false){
		parent::__construct($pageAccessLevel, $pageCheckEquals, $doHistory, $config);
	}

	/**
	 * Display screen shown on logging off
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

/* End of file Protect.php */
/* Location: ./cfg/Protect.php */