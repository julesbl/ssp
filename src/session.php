<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	session.php
*   Created:	08/01/2005
*   Descrip:	Session routines and configuration.
*
*   Copyright 2005-2015 Julian Blundell, w34u
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
*   Rev. Date	18/09/2015
*   Descrip:	Changed to an object oriented approach.
*/
//***********************************************************
// set up session functions

// database functions

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

class ssp_session_handler{
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
	 * @var SSP_DB 
	 */
	private $db;
	/**
	 * System Configuration
	 * @var SSP_Configuration
	 */
	private $cfg;
	
	public function __construct(){
		$this->cfg = SSP_Configuration::get_configuration();
		$this->db = SSP_DB::get_connection();
	}

	/**
	 * Set up the session handling
	 * @param type $save_path
	 * @param type $session_name
	 * @return type
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
$handler = new ssp_session_handler();

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

/* End of file session.php */
/* Location: ./sspincludes/session.php */