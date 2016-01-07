<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	API_interface.php
*   Created:	10/04/2009
*   Descrip:	Class used ti implement a remot login between two ssp systems
*   (currently not functioning).
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
*   Rev. Date	10/04/2009
*   Descrip:	Created.
*
*/
class API_interface{

	var $encryptionKey = ""; // encryption string
	var $path = ""; // url to remote SSP system api
	var $token = ""; // remote login token from remote system
	var $cookieFile = ""; // cookie storage file

	var $db; // db object
	var $sessionStatusTable = ""; // remote session status
	var $session; // session object
	var $remoteIpAddress = ""; // valid remote IP address

	var $command = ""; // current command
	var $data = array(); // prameters from a recieved command
	var $error = false; // message error
	var $errorString = ""; // error that has occured

	var $mirror = false;

	function API_interface($key, $path, $cookieFile){
		// contstructor

		if(strlen($key) > 24){
			$key = substr($key, 0, 24);
		}
		$this->encryptionKey = $key;

		$this->path = $path;
		$this->cookieFile = $cookieFile;
	}

	function setupSspInterface($session, $db, $table, $remoteIpAddress){
		// set up interface to local SSP system

		$this->session = $session;
		$this->sessionStatusTable = $table;
		$this->db = $db;
		$this->remoteIpAddress = $remoteIpAddress;
	}

	function parseMessage(){
		// parse posted message

		// abort routine on incorrect remote address
		if(strcmp(SSP_paddIp($this->remoteIpAddress), SSP_paddIp($_SERVER['REMOTE_ADDR'])) != 0){
			return(false);
		}

		$command = "";
		if(isset($_POST["message"])){
			if($messageString = $this->decrypt(SSP_stringRaw($_POST["message"]))){
				if($message = unserialize($messageString)){
					if(is_object($message) and strtolower(get_class($message)) == "api_message"){
						$this->command = $command = $message->command;
						$this->data = $message->data;
					}
				}
				else{
					$this->messageError($messageString, "Invalid message recieved after decryption");
				}
			}
		}

		switch($command){
			case "keepAlive":
				$this->keepAlive();
			break;

			case "loginSetup":
				$this->loginSetup();
			break;
			default:
				$this->errorReply("Unknown Command");
			break;
		}
	}

	function keepAlive(){
		// keep session alive in associated ssp system

		if(is_object($this->session) and method_exists($this->session, "keepAlive")){
			if($this->session->keepAlive($this->data["sessionId"])){
				$this->acknowledgeReplay();
			}
			else{
				$this->errorReply("No logged in session");
			}
		}
		else{
			$this->errorReply("Not an SSP system or no keep alive function");
		}
	}

	function loginSetup(){
		// sets up the system fo an automatic login from remote

		// check supplied data
		if(isset($this->data["userName"]) and isset($this->data["sessionId"]) and isset($this->data["accessLevel"]) and isset($this->data["userIp"])){
			if(is_object($this->session) and method_exists($this->session, "validRemoteUser")){
				if($this->session->validRemoteUser($this->data["userName"])){
					// check for existing remote login entry
					$where = array(
						"remoteSession" => $this->data["sessionId"],
						"userName" => $this->data["userName"],
						"userIp" => $this->data["userIp"],
						"localSession" => $this->session->sessionToken,
					);
					$remote = $this->db->get($this->sessionStatusTable, $where, "API Interface: Checking for current remote login record");
					if($remote){
						$loginToken = $remote->id;
					}
					else{
						// create remote user login entry
						$loginToken = SSP_uniqueId();
						$fields = array(
							"id" => $loginToken,
							"remoteSession" => $this->data["sessionId"],
							"userName" => $this->data["userName"],
							"localSession" => $this->session->sessionToken,
							"userIp" => $this->data["userIp"],
						);
						$this->db->insert($this->sessionStatusTable, $fields, "API Interface: Inserting new remote login record");
					}
					$this->data = array("loginToken"=> $loginToken);
					$this->acknowledgeReply();
				}
				else{
					$this->errorReply("Invalid remote user");
				}
			}
			else{
				$this->errorReply("Not an SSP system or has no validRemoteUser function");
			}
		}
		else{
			$this->errorReply("Invalid data supplied for remote login");
		}
	}

	function sendKeepAlive(){
		// send keep alive to a remote system

		$messageObject = new API_message("keepAlive");
		$messageObject->data["sessionId"] = $sessionId;

		$result = $this->sendMessage($messageObject);

		if($result and !$this->isError($result)){
			return(true);
		}
		else{
			return(false);
		}
	}

	function sendRemoteLoginSetup($userName, $sessionId, $accessLevel, $userIp){
		// send remote login setup to remote system

		$messageObject = new API_message("loginSetup");
		$messageObject->data["sessionId"] = $sessionId;
		$messageObject->data["userName"] = $userName;
		$messageObject->data["accessLevel"] = $accessLevel;
		$messageObject->data["userIp"] = $userIp;

		$result = $this->sendMessage($messageObject);

		if($result and !$this->isError($result) and isset($result->data["loginToken"])){
			$this->token = $result->data["loginToken"];
			return($this->token);
		}
		else{
			return(false);
		}
	}

	function remoteLogin($path, $token=""){
		// transfer to remote site and login

		$remoteSystem = curl_init($path);
		if($remoteSystem){
			//curl_setopt($remoteSystem, CURLOPT_COOKIEJAR, $this->cookieFile);
			//curl_setopt($remoteSystem,  CURLOPT_COOKIEFILE, $this->cookieFile);
			curl_setopt($remoteSystem, CURLOPT_RETURNTRANSFER, false);
			curl_setopt($remoteSystem, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($remoteSystem, CURLOPT_TIMEOUT, 2);
			if($this->mirror){
				curl_setopt($remoteSystem, CURLOPT_USERPWD, "gbattle:planet4766");
			}
			curl_setopt($remoteSystem, CURLOPT_POST, true);
			if($token != ""){
				$loginToken = $token;
			}
			else{
				$loginToken = $this->token;
			}
			curl_setopt($remoteSystem, CURLOPT_POSTFIELDS, array("remoteLoginToken" => $loginToken));
			curl_exec($remoteSystem);
			if(curl_errno($remoteSystem)){
				$this->error = true;
				$error = "Remote login: Curl error ". curl_errno($remoteSystem). ": ". curl_error($remoteSystem);
				$this->errorString = $error;
				$result = false;
			}
			curl_close($remoteSystem);
		}
		else{
			$this->error = true;
			$error = "Remote login: Connection error with ". $this->path;
			$this->errorString = $error;
		}
	}
	
	function autoLogin($userTable){
		// routine used in slave site to login remote user
		
		if(isset($_GET["remoteLoginToken"])){
			$where = array(
				"id" => $_GET["remoteLoginToken"],
				"userIp" => $_SERVER['REMOTE_ADDR'],
			);
			$remoteLoginRecord = $this->db->get($this->sessionStatusTable, $where, "SSP Protect: gettting auto login record");
			if($remoteLoginRecord){
				$where = array("UserName"=>$remoteLoginRecord->userName);
				$userLoginInfo = $this->db->get($userTable, $where, "SSP Protect: getting user login information");
				// create login record
				$login = new SSP_Logon($this, "", false, true);
				$login->logonCheck($userLoginInfo);
				session_write_close();
				SSP_Divert(SSP_Path());
			}
		}
	}

	function sendMessage($messageObject){
		// send message to remote system

		$messageString = $this->encrypt(serialize($messageObject));

		$remoteSystem = curl_init($this->path);
		if($remoteSystem){
			//curl_setopt($remoteSystem, CURLOPT_COOKIEJAR, $this->cookieFile);
			//curl_setopt($remoteSystem,  CURLOPT_COOKIEFILE, $this->cookieFile);
			curl_setopt($remoteSystem, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($remoteSystem, CURLOPT_HEADER, false);
			curl_setopt($remoteSystem, CURLOPT_TIMEOUT, 2);
			if($this->mirror){
				curl_setopt($remoteSystem, CURLOPT_USERPWD, "gbattle:planet4766");
				$result = curl_exec($remoteSystem);
			}
			curl_setopt($remoteSystem, CURLOPT_POST, true);
			curl_setopt($remoteSystem, CURLOPT_POSTFIELDS, array("message"=>$messageString));
			$result = curl_exec($remoteSystem);
			if($result){
				curl_close($remoteSystem);
				$result = unserialize($this->decrypt($result));
			}
			else{
				$this->error = true;
				$error = "Curl error ". curl_errno($remoteSystem). ": ". curl_error($remoteSystem);
				$this->errorString = $error;
				$result = false;
				curl_close($remoteSystem);
			}
		}
		else{
			$this->error = true;
			$error = "Connection error with ". $this->path;
			$this->errorString = $error;
			$result = false;
		}
		return($result);
	}

	function errorReply($error){
		// reply with error message

		$messageObject = new API_error($this->command, $error);
		$messageObject->data = $this->data;

		$this->replyMessage($messageObject);
	}

	function acknowledgeReply(){
		// send an acknowlege to a command

		$messageObject = new API_message("acknowledge");
		$messageObject->data = $this->data;

		$this->replyMessage($messageObject);
	}

	function replyMessage($messageObject){
		// reply to system sending message

		$messageString = serialize($messageObject);
		echo $this->encrypt($messageString);
	}

	function isError($message){
		// check for an API_error object

		if(is_object($message) and strtolower(get_class($message)) == "api_error"){
			$this->error = true;
			$this->errorString = $message->error;
			$this->command = $message->command;
			$this->data = $message->data;
		}
		elseif((is_object($message) and strtolower(get_class($message)) != "api_message") or !is_object($message)){
			$this->error = true;
			$this->errorString = "Invalid object or non-object as reply";
		}
		else{
			$this->error = false;
		}
		return($this->error);
	}

	function encrypt($string){

		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $this->encryptionKey, $iv);
		$encrypted_data = mcrypt_generic($td, $string);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		$result = serialize(new API_encrypted($encrypted_data, $iv));

        return($result);
	}

	function decrypt($string){

		$data = unserialize($string);

		if($data and strtolower(get_class($data)) == "api_encrypted"){
			$td = mcrypt_module_open('tripledes', '', 'ecb', '');
			$mcryptInit = mcrypt_generic_init($td, $this->encryptionKey, $data->iv);
			if($mcryptInit === false or $mcryptInit < 0){
				if($mcryptInit === false){
					$this->messageError("", "McryptInit invalid parameters");
				}
				if($mcryptInit < 0){
					$this->messageError("", "McryptInit invalid return of ". $mcryptInit);
				}
			}
			$result = mdecrypt_generic($td, $data->string);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
		}
		else{
			$this->messageError($string, "Invalid message for decryption");
		}
        return($result);
	}
	
	function messageError($message, $error){
		// kills the function and replies with a message
		die($error. " on ". $_SERVER['SERVER_NAME']. ": starts=== <br />!". $message. "!<br />===ends");
	}
}

class API_message{
	var $command = ""; // command
	var $data = array(); // parrameters for command

	function API_message($command){
		$this->command = $command;
	}
}

class API_error{
	var $command = "";
	var $error = "";
	var $data = array();

	function API_error($command, $error){
		$this->command = $command;
		$this->error = $error;
	}
}

class API_encrypted{
	// encrypted string class

	var $string = "";
	var $iv = "";

	function API_encrypted($string, $iv){
		$this->string = $string;
		$this->iv = $iv;
	}
}
/* End of file API_interface.php */
/* Location: ./sspincludes/API_interface.php */