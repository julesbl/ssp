<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	SspDb.php
*   Created:	07/05/2008
*   Descrip:	Adodb database classe
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
*   Rev. Date	07/05/2008
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	23/02/2011
*   Descrip:	Changed to php5 class system.
*
*   Revision:	c
*   Rev. Date	13/01/2016
*   Descrip:	Changed to psr-4.
*/

namespace w34u\ssp;

class SspDb{
	// database routines for Adodb

	/** @var ADOConnection adodb database connection */
	public $db;
	/** @var SSP_Configuration - ssp configuration object */
	private $cfg;
	public $result; // adodb result from a query
	/** @var bool - database is connected */
	public $connected = false;
	/** @var array - saved results between looped sql operations */
	private $results = array();
	/** @var bool - error durinf sql operation */
	public $error = false;
	/** @var string - error description */
	private $errorDescription = "";
	/** @var bool - abort execution on error */
	public $abortOnError = true;
	/** @var bool - enquote field names */
	private $quote = false;
	// quoting characters:
	// mysql - ` `, access & mssql - [ ]
	// PosgreSql and most other databases - " "
	/** @var string - enquote start character */
	private $quoteStart = '`';
	/** @var string - enquote end character */
	private $quoteEnd = '`';
	
	/** @var bool - cache queries */
	public $cache = false;
	/** @var int - time for which queries are cached in seconds */
	public $cacheTime = 300;
	
	/** @var bool - debug display */
	var $dbg = false;
	
	/**
	 * Static database connection
	 * @var SSP_DB
	 */
	private static $connection = null;

	/**
	 * Constuctor
	 *
	 * @param string $dsn adodb database connection string
	 * @param array $config not used with adodb
	 * @param SSP_Configuration $cfg SSP configuration object
	 */
	public function __construct($dsn){
		// constructor

		$this->db = ADONewConnection($dsn);
		$this->cfg = Configuration::getConfiguration();
		$this->error("SSP Session: Opening Database", $dsn);

		if(!$this->error){
			if(isset($this->quoteList[$this->db->databaseType])){
				$this->nameQuote = $this->quoteList[$this->db->databaseType];
			}
			if($this->cfg->connectionEncoding != ""){
				$this->db->EXECUTE("set names '{$this->cfg->connectionEncoding}'");
				$this->error("Setting connection encoding");
			}
		}

		if(!$this->error){
			$connected = true;
		}
	}

	/**
	 * Does an adodb prepare and execute with the supplied query and data.
	 * Resistant to SQL injection.
	 *
	 * @param string $query parameterised query to be executed with the values supplied.
	 * @param array $values set ov values to replace parameters in query.
	 * @param string $errorString string to be sent to SSP_DBError on query failure.
	 * @return - adodb result on success or failure.
	 */
	function query($query, $values, $errorString){
		
		if($this->dbg){
			echo 'Sql debug: '. $query. "<br />". $errorString. '<br />';
			echo '<pre>';
			var_dump($values);
			echo '</pre>';
		}

		if($this->cache){
			$this->result = $this->db->CacheExecute($this->cacheSize, $query, $values);
		}
		else{
			$this->result = $this->db->Execute($query, $values);
		}
		$this->error($errorString. " Execute", $query);

		return($this->result);
	}
	
	/**
	 * Move to specified result row
	 * @param int $row 
	 */
	public function move($row){
		// moves the pointer to the specified line
		$this->result->move($row);
	}

	/**
	 * Fetch row of data for current result
	 * @param bool $array - return as array
	 * @param int $start - get a particular record
	 * @return object or array
	 */
	public function fetchRow($array=false, $start = 0){
		// returns a line from a database result
		//
		// parameters
		//	$mode - bool -
		//		true, returns an assciative array
		//		false, returns and object
		//	$start - integer - record to return

		if($start){
			$this->result->move($start);
		}

		if($array){
			return($this->result->fetchRow());
		}
		else{
			$return = $this->result->FetchNextObject(false);
			return($return);
		}
	}

	/**
	 * Gets the number of rows returned
	 * @return int 
	 */
	public function numRows(){
		return($this->result->RecordCount());
	}

	/**
	 * Number of rows affected
	 * @return int 
	 */
	public function affectedRows(){
		return($this->db->Affected_Rows());
	}

	/**
	 * Count the number of records
	 * @param string $tableName - name of table to query
	 * @param string $field - name of field to count
	 * @param array $where - fields and values to use in the where condition
	 * @param string $errorString - string to be shown on error
	 * @return int record count
	 */
	public function count($tableName, $field, $where, $errorString){
		$query = "select count({$this->qt($field)}) as fieldCount from {$this->qt($tableName)} where ";
		$whereFields = $this->qt(array_keys($where));
		$values = array_values($where);
		$fields = array();
		foreach($whereFields as $field){
			$fields[] = $field. " = ?";
		}
		$query .= implode(" and ", $fields);
		$this->query($query, $values, $errorString);
		if($this->numRows()){
			$row = $this->fetchRow();
			$result = $row->fieldCount;
		}
		else{
			$result = 0;
		}
		return($result);
	}

	/**
	 * Get the specfied record as an object
	 * If multiple records are returned, the first one is returned
	 * @param string $tableName - name of the table
	 * @param array $whereValues - field names and values for where condition
	 * @param string $errorString - error string
	 * @return object - returned line as an object
	 */
	function get($tableName, $whereValues, $errorString){
		if(!is_array($whereValues)){
			trigger_error("SSP DB: get requires param whereValues to be an array", E_USER_ERROR);
		}
		$query = "select * from ". $this->qt($tableName). " where ";
		$whereFields = $this->qt(array_keys($whereValues));
		$values = array_values($whereValues);
		foreach($whereFields as $key => $value){
			$whereFields[$key] = $value. " = ?";
		}
		$query .= implode(" and ", $whereFields);
		$result = $this->query($query, $values, $errorString);
		if($result and $result->numRows()){
			$record = $this->fetchRow();
		}
		else{
			$record = false;
		}
		return($record);
	}

	/**
	 * Gets the specified fields from a single record from the
	 * specified table based upon the where condition values
	 * @param string $tableName - name of the table in the database
	 * @param array $fields - list of fields to be returned
	 * @param array $whereValues - field names and values for the where statement, and only.
	 * @param string $errorString - error issued on query failure
	 * @return bool/object - false on no result, 
	 */
	function getf($tableName, $fields, $whereValues, $errorString){
		if(!is_array($fields)){
			trigger_error("SSP DB: getf requires param fields to be an array", E_USER_ERROR);
		}
		if(!is_array($whereValues)){
			trigger_error("SSP DB: getf requires param whereValues to be an array", E_USER_ERROR);
		}
		$fields = $this->qt($fields);
		$fieldString = implode(", ", $fields);
		$query = "select $fieldString from ". $this->qt($tableName). " where ";
		$whereFields = $this->qt(array_keys($whereValues));
		$values = array_values($whereValues);
		foreach($whereFields as $key => $value){
			$whereFields[$key] = $value. " = ?";
		}
		$query .= implode(" and ", $whereFields);
		$result = $this->query($query, $values, $errorString);
		if($result and $result->numRows()){
			$record = $this->fetchRow();
		}
		else{
			$record = false;
		}
		return($record);
	}

	/**
	 * Get records from the specified table based upon the where condition values
	 * Use fetchRow to retrieve the rows
	 * @param string $tableName - name of the table in the database
	 * @param array $whereValues - field names and values for the where statement, and only.
	 * @param array $orderBy - array of fields to sortby "fieldName1" => "asc", "fieldName2" => "desc"
	 * @param string $errorString - error issued on query failure
	 * @return bool/int - false on no records else number of rows
	 */
	function getl($tableName, $whereValues, $orderBy, $errorString){
		if(!is_array($whereValues)){
			trigger_error("SSP DB: getl requires param whereValues to be an array", E_USER_ERROR);
		}
		$query = "select * from ". $this->qt($tableName);
		if(count($whereValues)){
			$where = true;
		}
		else{
			$where = false;
		}

		if($where){
			$query .= " where ";
			$whereFields = $this->qt(array_keys($whereValues));
			$values = array_values($whereValues);
			foreach($whereFields as $key => $value){
				$whereFields[$key] = $value. " = ?";
			}
			$query .= implode(" and ", $whereFields);
		}
		else{
			$values = array();
		}
		if(count($orderBy)){
			$orderBy = $this->qt($orderBy);
			$orderByStrings = array();
			foreach($orderBy as $field => $order){
				if(strtolower($order) == "asc"){
					$orderByStrings[] .= $field. " asc";
				}
				elseif(strtolower($order) == "desc"){
					$orderByStrings[] .= $field. " desc";
				}
			}
			if(count($orderByStrings)){
				$query .= " order by ". implode(", ", $orderByStrings);
			}
		}

		$result = $this->query($query, $values, $errorString);
		if($result and $number = $result->numRows()){
			$record = $number;
		}
		else{
			$record = false;
		}
		return($record);
	}

	/**
	 * Deletes a record from the specified table based upon the where condition values
	 * @param string $tableName - name of the table in the database
	 * @param array $whereValues - field names and values for the where statement, and only.
	 * @param string $errorString - error issued on query failure
	 * @return int - 0 on no rows affected 
	 */
	function delete($tableName, $whereValues, $errorString){
		if(!is_array($whereValues)){
			trigger_error("SSP DB: delete requires param whereValues to be an array", E_USER_ERROR);
		}
		$query = "delete from ". $this->qt($tableName). " where ";
		$whereFields = $this->qt(array_keys($whereValues));
		$values = array_values($whereValues);
		foreach($whereFields as $key => $value){
			$whereFields[$key] = $value. " = ?";
		}
		$query .= implode(" and ", $whereFields);
		$this->query($query, $values, $errorString);

		return($this->affectedRows());
	}

	/**
	 * Gets a record from the specified table based upon the where condition values
	 * @param string $tableName - name of the table in the database
	 * @param array $fieldValues - values to be updated fieldName => value
	 * @param array $whereValues  - field names and values for the where statement, and only.
	 * @param string $errorString  - error issued on query failure
	 * @return bool - true on succesful update 
	 */
	function update($tableName, $fieldValues, $whereValues, $errorString){
		if(!is_array($fieldValues)){
			trigger_error("SSP DB: update requires param fieldValues to be an array", E_USER_ERROR);
		}
		if(!is_array($whereValues)){
			trigger_error("SSP DB: update requires param whereValues to be an array", E_USER_ERROR);
		}
		$query = "update ". $this->qt($tableName). " set ";
		$updateFields = $this->qt(array_keys($fieldValues));
		foreach($updateFields as $key => $value){
			$updateFields[$key] = $value. "= ?";
		}
		$query .= implode(", ", $updateFields);
		$values = array_values($fieldValues);
		$whereFields = $this->qt(array_keys($whereValues));
		$values = array_merge($values, array_values($whereValues));
		foreach($whereFields as $key => $value){
			$whereFields[$key] = $value. " = ?";
		}
		$query .= " where ". implode(" and ", $whereFields);

		$result = $this->query($query, $values, $errorString);
		if($this->affectedRows()){
			$record = true;
		}
		else{
			$record = false;
		}
		return($record);
	}

	/**
	 * Gets a record from the specified table based upon the where condition values
	 * @param string $tableName - name of the table in the database
	 * @param array $fieldValues - values to be inserted fieldName => value
	 * @param string $errorString - error issued on query failure
	 * @return bool - true on success 
	 */
	function insert($tableName, $fieldValues, $errorString){
		if(!is_array($fieldValues)){
			trigger_error("SSP DB: insert requires param fieldValues to be an array", E_USER_ERROR);
		}
		$query = "insert into ". $this->qt($tableName). " (";
		$updateFields = $this->qt(array_keys($fieldValues));
		$query .= implode(", ", $updateFields). ") values(";
		$quest = array();
		$quest = array_pad($quest, count($updateFields), "?");
		$query .= implode(", ", $quest). ")";
		$values = array_values($fieldValues);

		$result = $this->query($query, $values, $errorString);
		if($this->affectedRows()){
			$record = true;
		}
		else{
			$record = false;
		}
		return($record);
	}

	/**
	 * Save current result onto the stack 
	 */
	function resultPush(){
		$this->saveResult();
	}

	/**
	 * Save current result to a location, same ad resultPush if $id == 0
	 * @param int/string $id 
	 */
	function saveResult($id=0){
		// backup result so another query can be run

		if($id != 0){
		$this->results[$id] = $this->result;
		}
		else{
			array_push($this->results, $this->result);
		}
	}
	
	/**
	 * Pop last result from the stack 
	 */
	function resultPop(){
		$this->getResult();
	}

	/**
	 * Get a result from a position, same as resultPop id $id == 0
	 * @param int/string $id 
	 */
	function getResult($id=0){
		// restore result so the result can be used again

		if($id != 0){
		$this->result = $this->results[$id];
		}
		else{
			$this->result = array_pop($this->results);
		}
	}

	/** 
	* If database error display results and perhaps exit program
	* @param string $errorString Error string from program attempting the database connection
	* @param string $query query that cause the error
	* @param array $values array of values passed to the query
	*
	* return - false on no error
	*/
	function error($errorString, $query="", $values=""){

		global $session;

		$errorSql = false;
		if(!$this->db){
			// database object is broken
			$errorSql = true;
			$error = "\nDatabase error ". date('d/m/Y H:i:s');
			$error .= "\nPackege Error: ". $errorString. "\nQuery: ". $query. "\n";
			$this->errorDescription = $error;
		}
		elseif($this->db->ErrorNo() != 0){
			// normal error
			$errorSql = true;
			$error = "\nDatabase error ". date('d/m/Y H:i:s');
			$error .= sprintf("\nRoutine producing error: %s\n", $_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF']);
			$error .= "\nPackege Error: ". $errorString. "\nQuery: ". $query. "\n";
			$error .= sprintf("ADODB Error: [%d]: %s\n", $this->db->ErrorNo(),  $this->db->ErrorMsg());
			if(is_array($values) and count($values)){
				"\nSql replacement arguments";
				"'". implode("', '", $values). "'\n";
			}
			if(trim($_SERVER['QUERY_STRING']) != ""){
				$error .= sprintf("\nQuery string: %s\n", $_SERVER['QUERY_STRING']);
			}
			$error .= "Debug backtrace\n";
			$backtrace = debug_backtrace();
			foreach($backtrace as $routine){
				if(!isset($routine['file'])){
					// probably call_user_func
					$error .= "call_user_func\n";
				}
				else{
					$error .= "Line ". $routine['line']. " of ". $routine['file']. "\n";
					if(isset($routine['function'])){
						$error .= " In function ". $routine['function']. "\n";
						if(isset($routine['args']) and is_Array($routine['args'])){
							$error .= "  Arguments ";
							foreach($routine['args'] as $arg => $argValue){
								if(is_array($argValue) or is_object($argValue)){
									$argValue = serialize($argValue);
								}
								$error .= $arg. " = '". $argValue. "' ";
							}
							$error .= "\n";
						}
					}
				}
			}
			$error .= sprintf("Users remote IP address: %s\n", $_SERVER['REMOTE_ADDR']);
			if(is_object($session) and $session->loggedIn){
				// put in user info if available
				$error .= sprintf("Users name: %s\n", UserAdmin::getName($session->userId, false));
				$error .= sprintf("Users id: %s\n", $session->userId);
			}
			$this->errorDescription = $error;
		}
		if($errorSql){
			$this->error = true;
			if($this->cfg->displaySqlFaults){
				echo '<head></head><body><pre>';
				echo $this->errorDescription;
				echo '</pre></body>';
			}
			else{
				error_log($error, 3, $this->cfg->errorLog);
				foreach($this->cfg->errorAdmins as $toAddress => $toName){
					ECRIAmailer("SSP SQL error handler", $this->cfg->noReplyEmail, $toName, $toAddress, "SSP SQL error on ". $this->cfg->siteName, $error);
				}
			}
			if($this->abortOnError){
				die("<p>Database error: Information has been emailed to admin</p>");
			}
		}
		return($this->error);
	}

	/**
	 * Enable quoting of field names
	 * @param bool $enable 
	 */
	function quote($enable = true){
		// enable quoting of field names

		$this->quote = $enable;
	}

	/**
	 * Set field quoting characters
	 * @param string $quoteStart - character to start for quoting a field, [ for MSSql
	 * @param string $quoteEnd - character to end for quoting a field, ] for MSSql
	 */
	function setQuote($quoteStart, $quoteEnd){
		// set quotes

		$this->quoteStart = $quoteStart;
		$this->quoteEnd = $quoteEnd;
	}

	/**
	 * Quote a field or an array of fields
	 * @param string/array of string $fields - field or fields
	 * @return string/ array of sting 
	 */
	public function qt($fields){
		// puts the appropriate quotes round field or a list of fields
		// and concats them with the supplied string
		// replaces any . with "." e.g. dbase.table.field becomes [dbase].[table].[field] for MSSql

		if($this->quote and is_string($fields)){
			$result = $this->quoteStart. 
				str_replace('.', $this->quoteEnd. '.'. $this->quoteStart, trim($fields)). 
					$this->quoteEnd;
		}
		elseif($this->quote){
			foreach($fields as $key => $field){
				$result[$key] = $this->quoteStart. 
					str_replace('.', $this->quoteEnd. '.'. $this->quoteStart, trim($field)). 
						$this->quoteEnd;
			}
		}
		else{
			$result = $fields;
		}

		return($result);
	}
	
	/**
	 * Get the database connection, if necessary create the database connection
	 * @return sspDb
	 */
	public static function getConnection(){
		$SSP_Config = Configuration::getConfiguration();
		
		if(self::$connection === null){
			self::$connection = new SspDb($SSP_Config->dsn);
		}
		return self::$connection;
	}
}
/* End of file SspDb.php */
/* Location: ./src/SspDb.php */