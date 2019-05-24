<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	ProtectConfig.php
*   Created:	4/02/2016
*   Descrip:	Configures the session.
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
*   Rev. Date	07/01/2005
*   Descrip:	Moved from ProtectBase.php.
*/

namespace w34u\ssp;

class ProtectConfig{
	/**
	 * Do not prevent page cacheing
	 * @var bool 
	 */
	public $pageCaching = true;
	/**
	 * Set page expiry in seconds, 0 - no expiry
	 * @var int
	 */
	public $pageValid = 0;
	/**
	 * If true will not divert to login on fail
	 * @var bool
	 */
	public $noLoginDivert = false;
	/**
	 * text returned on session fail but no login divert
	 * @var string
	 */
	public $noLoginDivertText = 'fail';
	/**
	 * Force SSL path, ensure that an ssl path is being used
	 * @var bool
	 */
	public $forceSSLPath = false;
	/**
	 * on true the rolling cookie will not be updated
	 * @var bool
	 */
	public $noCookieUpdate = false;
	/**
	 * Array of strings specifying which pages should not be included in the history
	 * @var array
	 */
	public $noHistoryPages = array();
	/**
	 * Send the ssl security headers
	 * @var bool
	 */
	public $sslSendHeaders = true;
	/**
	 * Debuging enabled
	 * @var bool
	 */
	public $debug = false;
	
	/**
	 * Add to pages not to be included in the history
	 * @param string $page - full of partial path to page
	 */
	public function addNoHistory($page){
		$this->noHistoryPages[] = $page;
	}
}
/* End of file ProtectConfig.php */
/* Location: ./src/ProtectConfig.php */