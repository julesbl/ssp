<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	CheckDataBase.php
*   Created:	25/09/2009
*   Descrip:	Set of classes to check data returned by forms and get parameters.
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
*   Rev. Date	25/09/2009
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

/**
 * Class to do data checking of input data
    dataType, can be:
        text - 0 to 9, a to z, A to Z, CR, TAB, \', ", +, -, _ space comma () /
        password - 0 to 9, a - z, A - Z
        dom - <= 128 char, 0-9, a-z, A-Z, ._,/: at least one dot
        domchk - checks the domain
        email - <= 128 char, @, 0-9, a-z, A-Z, _.+-
        emailchk - also checks the domain
		date - 0 to 9, / a to z, A to Z
		time - 0 to 9, :
        phone - 0-9() +-. space
        int - 0-9 -
        real - 0-9 . - e
        hex - 0-9, a-f, A-F
        oct - 0-7
        bin - 0,1
        lable - 0 to 9, a - z, A - Z, -, _
        gen - any character - when re-displayed any special characters are converted to html special entities and then converted back to characters on submission
 */
abstract class CheckDataBase{
	
	/** @var int error number, used to index error messages */
	public $error = 0;
	/** @var string error message generated, depends on what is being checked */
	public $errorMessage = "";
	/** @var array data check types used for checking various data types */
	public $dataTypes = array();
	/** @var bool attempt to translate the text */
	private static $translate = false;
	/** @var object translator object use to translate strings */
	private static $tranlator;
	/** @var string name of method used in tranlation object */
	private static $translateMethod = 't';
	
	/**
	 * constructor
	 */
	function __construct(){
		
		$this->addDataCheck("text",
							'alphanumeric characters, CR, TAB, \', ", +, -, _ space comma () / ! [] and ?',
							".'\"+\-_\s,\/:%()!?\[\]\{\}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}\p{Pd}");
		$this->addDataCheck("password",
							'0 to 9, a to z, A to Z and other 7 bit ascii chars like # $ etc.',
							"\\x20-\\x7E\_");
		$this->addDataCheck("date",
							'0 to 9, / space comma, a to z, A to Z',
							"0-9a-zA-Z\/ ,");
		$this->addDataCheck("time",
							'0 to 9, :',
							"0-9:");
		$this->addDataCheck("phone",
							'0 to 9, () + - . space',
							"0-9()+\-. ");
		$this->addDataCheck("int",
							'0 to 9, - ',
							"0-9+\-");
		$this->addDataCheck("real",
							'0 to 9, .- e +',
							"0-9+\-e.");
		$this->addDataCheck("hex",
							'0 to 9, a to z, A to Z',
							"0-9a-zA-Z");
		$this->addDataCheck("oct",
							'0 to 7',
							"0-7");
		$this->addDataCheck("bin",
							'0 or 1',
							"01");
		$this->addDataCheck("email",
							'0 to 9, a to z, A to Z, .-_+ @',
							"a-zA-Z0-9.\-_+\@");
		$this->addDataExtend("emailchk", "email");
		$this->addDataCheck("dom",
							'0 to 9, a to z, A to Z, .-_+ # / : ? % ~ [] @ ! $ = ; , &',
							"a-zA-Z0-9.\-_+#\/:\?\%~\[\]@!$=;,&");
		$this->addDataExtend("domchk", "dom");
		$this->addDataCheck("lable",
							'0 to 9, a to z, A to Z, -_',
							"0-9a-zA-Z\-_");
		$this->addDataExtend("gen", "gen");
	}

	/**
	 * Add a data check type to the field data checks array
	 * @param string $type - type of check
	 * @param string $errorMessage - error message to be produced on failure
	 * @param string $validChars - a string used in pregMatch, valid characters
	 */
	function addDataCheck($type, $errorMessage, $validChars){
		$this->dataTypes[$type] = new TypeDataCheck($type, $errorMessage, $validChars);
	}
	
	/**
	 * Specifies a type that checks the url is valisd
	 * @param string $type - type of check
	 * @param string $typeExtend - main type to do first before url check
	 */
	function addDataExtend($type, $typeExtend){
		$this->dataTypes[$type] = $typeExtend;
	}
	
	/**
	 * Check a data has only the correct characters within it
	 * @param string $type - required type of data - text, password, data, time, phone, int,
	 * 							real, hex, oct, bin, email, emailchk, dom, domchk, lable, gen
	 * @param string $data - data to be checked
	 * @return int - error number, 0 no error
	 */
	function check($type, $data){
		
		$type = strtolower($type);
		
		if(!isset($this->dataTypes[$type])){
			SSP_error("Unknown data type ". $type);
		}
		else{
			// general data type, do not check
			if(strcmp($type, "gen") == 0){
				$this->error = 0;
				$this->errorMessage = "";
				return($this->error);
			}
			
			// check for dns checking of urls
			if(is_string($this->dataTypes[$type])){
				$type = $this->dataTypes[$type];
				$checkDns = true;
			}
			else{
				$checkDns = false;
			}
			
			// check the data
			if($this->dataTypes[$type]->check($data, $checkDns)){
				$this->error = $this->dataTypes[$type]->error;
				$this->errorMessage = $this->t($this->dataTypes[$type]->errorMessage);
			}
			else{
				$this->error = 0;
				$this->errorMessage = "";
			}
		}
		return($this->error);
	}
	
	/**
	 * check a data is of correct type
	 * @param string $type - required type of data - text, password, data, time, phone, int,
	 * 							real, hex, oct, bin, email, emailchk, dom, domchk, lable, gen
	 * @param various $data value of variable to be checked
	 * @return int - default on datacheck failure
	 */
	public function parseParam($type, $data, $default){
		if($this->check($type, $data) != 0){
			$result = $default;
		}
		else{
			$result = $data;
		}
		return $result;
	}
	
	/**
	 * Check a data type exists
	 * @param string $type - type of string to check
	 */
	public function isType($type){
		if(isset($this->dataTypes[strtolower($type)])){
			$exists = true;
		}
		else{
			$exists = false;
		}
		return($exists);
	}
	
	/**
	 * Configure translation object and method
	 * @param object $translationObject
	 * @param string $translationMethod
	 */
	public static function addTranslation($translationObject, $translationMethod = 't'){
		self::$translate = true;
		self::$tranlator = $translationObject;
		self::$translateMethod = $translationMethod;
	}
	
	/**
	 * Attempt to translate a string of text
	 * @param type $text
	 * @return type
	 */
	private function t($text){
		if(self::$translate){
			$translateMethod = self::$translateMethod;
			return(self::$tranlator->$translateMethod($text));
		}
		else{
			return($text);
		}
	}
}

/**
 * class to check particular data types
 * @var int $error error produced, 0 - no error
 * 					1 - character error
 * 					2 - url too long
 * 					3 - no dot in url
 * 					4 - url does not exist
 * 					5 - email too long
 * 					6 - email no @ and dot
 * @var string $type type of data expected, text, password, date, time, phone, int,
 * 					real, hex, oct, bin, email, dom, lable
 * @var string $errorMessage description of error characters
 * @var string $validChars list of valid characters for this type
 */
class TypeDataCheck{
	
	/** @var integer - error number */
	public $error = 0; 
	/** @var string - type of check */
	private $type = "";
	/** @var string - valid character message */
	public $errorMessage = "";
	/** @var string - valid characters */
	public $validChars = "";
	
	/**
	 * Constructor
	 * @param string $type - type of check
	 * @param string $errorMessage - error message to be returned on invalid char
	 * @param type $validChars - characters which are valid
	 */
	function __construct($type, $errorMessage, $validChars){
		// constructor
		$this->type = $type;
		$this->errorMessage = $errorMessage;
		$this->validChars = $validChars;
	}
	
	function check($data, $checkDns=false){
		
		if(preg_match("/[^". $this->validChars. "]/u", trim($data))){
			$this->error = 1;
		}
		elseif(strcmp($this->type, "dom") == 0){
			$this->checkDom($data, $checkDns);
		}
		elseif(strcmp($this->type, "email") == 0){
			$this->checkEmail($data, $checkDns);
		}
		else{
			$this->error = 0;
		}
		return($this->error);
	}
	
	/**
	 * check url/dom specific items
	 * @param string @data data to be checked
	 * @param bool @checkDns if true checks to see if the domain exists
	 */
	function checkDom($data, $checkDns){
		
		if(!preg_match("/\\./", trim($data))){
			$this->error = 3;
		}
		elseif($checkDns && !checkdnsrr($data, "ANY")){
			$this->error = 4;
		}
		else{
			$this->error = 0;
		}
		return($this->error);
	}

	/**
	 * check email specific items
	 * @param string @data data to be checked
	 * @param bool @checkDns if true checks to see if the domain exists
	 */
	function checkEmail($data, $checkDns){
		
		if(strlen(trim($data)) > 128){
			$this->error = 5;
		}
        elseif(!preg_match("/^([^@]+)@(.*)$/", trim($data), $parts)){
            $this->error = 6;
        }
        else{
            $user = $parts[1];
            $domain = $parts[2];
			if(!$this->checkDom($domain, $checkDns)){
				$this->error = 0;
			}
        }
		return($this->error);
	}
	
}
/* End of file CheckDataBase.php */
/* Location: ./sspincludes/CheckDataBase.php */