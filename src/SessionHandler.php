<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	SessionHandler.php
*   Created:	08/01/2005
*   Descrip:	Session routines and configuration.
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
*   Rev. Date	08/01/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	18/09/2015
*   Descrip:	Changed to an object oriented approach.
*
*   Revision:	c
*   Rev. Date	13/01/2016
*   Descrip:	Changed to psr-4.
*/
//***********************************************************

/*
Table format
SessionId char(32) - Session Id created by PHP routines
SessionTime int - Unix timestamp in seconds from PHP
SessionName char(20) - Session name
SessionIp (30) - IP address of current session, read by logon routine.
SessionUserIp (30) - Valid IP address for a particular user.
SessionCheckuserIp int - 0 - default action as specifed in config, 1 - check user is at correct IP address.
SessionData blob - data saved by sesssion, max 65535 bytes, if you are going to save more extend this to medium blob
*/

namespace w34u\ssp;

class SessionHandler{
	/**
	 * Save path used for file based sesion handling
	 * @var string
	 */
	private $save_path = '';
	/**
	 * Session name
	 * @var string
	 */
	private $session_name = '';
	/**
	 * Database connection
	 * @var SspDb
	 */
	private $db;
	/**
	 * System Configuration
	 * @var Configuration
	 */
	private $cfg;
	
	public function __construct(){
		$this->cfg = Configuration::getConfiguration();
		$this->db = SspDb::getConnection();
		// set session name if a site crawling bot
		if($this->bot_detected()){
			session_id($this->cfg->sessBotDetectionId);
		}
	}

	/**
	 * Detect if bot is scanning the site
	 * @return bool
	 */
	private function bot_detected(){
		if(isset($_SERVER['HTTP_USER_AGENT']) and
			preg_match($this->cfg->sessBotDetectionPattern, $_SERVER['HTTP_USER_AGENT'])){
			return true;
		}
		return false;
	}

	/**
	 * Set up the session handling
	 * @param string $save_path
	 * @param string $session_name
	 * @return boolean
	 */
	public function open($save_path, $session_name){
		$this->save_path = $save_path;
		$this->session_name = $session_name;
		return($this->db->connected);
	}
	
	/**
	 * Close the session
	 * @return bool - true on succesful shutdown
	 */
	public function close(){
		// close down session handling
		return(true);
	}

	/**
	 * Read the session data form the database
	 * @param string $id - session id
	 * @return object
	 */
	public function read($id){
		$where = array(
			"SessionId" => $id,
			"SessionName" => $this->session_name,
			);
		$row = $this->db->get($this->cfg->sessionTable, $where, "SSP Session routines: reading session data");

		if($this->db->numRows() == 0){
			$returns = "";
		}
		else {
			$returns = $row->SessionData;
		}
		return($returns);
	}

	/**
	 * Write the session data
	 * @param string $id - session id
	 * @param object $sess_data - session data
	 * @return bool  - true on success
	 */
	public function write ($id, $sess_data) {
		if($this->db->error){
			die();
		}

		// check if session already exists
		$where = array(
			"SessionId" => $id,
			"SessionName" => $this->session_name,
		);
		$this->db->get($this->cfg->sessionTable, $where, "SSP Session: Check session exists for writing data");

		if($this->db->numRows()){
			// update a current session
			$fields = array(
				"SessionData" => $sess_data,
				"SessionId" => $id,
				"SessionName" => $this->session_name,
				"SessionTime" => time()
			);
			$this->db->update($this->cfg->sessionTable, $fields, $where, "SSP Session routines: updating session data");
		}
		else {
			// write a new session record
			$fields = array("SessionData" => $sess_data,
				"SessionId" => $id,
				"SessionName" => $this->session_name,
				"SessionTime" => time()
			);
			$this->db->insert($this->cfg->sessionTable, $fields, "SSP Session routines: writing new session data");
		}

		return(true);
	}

	/**
	 * Destroy current users session
	 * @param string $id - session id
	 * @return bool - true on success
	 */
	public function destroy($id){
		// Create delete query
		$where = array(
			"SessionId" => $id,
			"SessionName" => $this->session_name,
		);
		$this->db->delete($this->cfg->sessionTable, $where, "SSP Session: Destroying current users session data");

		return(true);
	}

	/**
	 * Clean up old session data etc
	 * @param int $maxlifetime - maximum old sesion lifetime in seconds
	 * @return bool - true on success
	 */
	public function gc($maxlifetime){
		// destroy any abandoned sessions after $maxlifetime in seconds

		// create delete query
		$query = "delete from ".$this->cfg->sessionTable." where ". $this->db->qt("SessionTime"). " < ?";
		$values = array((time()-$maxlifetime));
		$this->db->query($query, $values, "SSP Session: Clean up old sessions");

		// clean up token table
		SSP_CleanToken();
		SSP_ResponseClean();
		return(true);
	}
}

/* End of file SessionHandler.php */
/* Location: ./src/SessionHandler.php */