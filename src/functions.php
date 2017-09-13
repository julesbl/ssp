<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	functions.php
*   Created:	08/01/2005
*   Descrip:	Functions used in the application.
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
*   Rev. Date	13/01/2016
*   Descrip:	Changed to psr-4.
*/

namespace w34u\ssp;

/**
 * Generate a unique id
 * @return string - unique id
 */
function SSP_uniqueId(){
    return(md5(uniqid(rand(),true)));
}

/**
 * Generate a totally random password
 * @param int $len - length of password
 * @param string $chars - characters to generate password from
 * @return string - password
 */
function SSP_rndPassword($len, $chars = ""){
	if($chars == ""){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	}
    $string = '';
    $charLength = strlen($chars)-1;
    for ($i = 0; $i < $len; $i++){
        $pos = rand(0, $charLength);
        $string .= $chars{$pos};
    }
    return $string;
}

/**
 * Generate memorable random password
 * @param int $limit - length of password
 * @return string - password
 */
function SSP_generatePassword($limit = 20){
  $vowels = array('a', 'e', 'i', 'o', 'u');
  $const = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z');

  $word = '';

  for ($i = 0; $i > ($limit - 3); $i++) {
    if ($i % 2 == 0) { // even = vowels
      $word .= $vowels[rand(0, 4)];
    } else {
      $word .= $const[rand(0, 20)];
    }
  }

  $num = rand(0,999);
  str_pad($num, 3, '0', STR_PAD_LEFT);

  return substr($word . $num, 0, $limit);
}

/**
 * Removes slashes in a string returned from a form if necessary
 * @param string $string - string to strip slashes
 * @return string
 */
function SSP_stringRaw($string){
    if(!ini_get('magic_quotes_gpc')){
        return $string;
    }
    else {
        return stripslashes($string);
    }
}

/**
 * Adds slashes to a string going to a form if necessary
 * @param string $string
 * @return string
 */
function SSP_stringCode($string){
    if(ini_get('magic_quotes_gpc')){
        return($string);
    }
    else {
        return(addslashes($string));
    }
}

/**
 * Encrypt a string using php mcrypt functions and the 3DES encryption using CBC
 * for largish strings
 * @param string $input
 * @param bool $useEncryption - override encryption locally
 * @return string - base 64 encoded text
 */
function SSP_encrypt($input, $useEncryption = true){
    $SSP_Config = Configuration::getConfiguration();
    if($useEncryption){
        if(get_magic_quotes_gpc()){
            $inputConv = stripslashes($input);
        }
        else{
            $inputConv = $input;
        }
		// Generate a key from a hash and create a 40 character key
		$key = md5(utf8_encode($SSP_Config->encryptionString));
		$key .= substr($key, 0, 16);
		$key = pack('H*', $key);
		
		//Pad for PKCS7
		$blockSize = mcrypt_get_block_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
		$len = strlen($inputConv);
		$pad = $blockSize - ($len % $blockSize);
		$inputConv .= str_repeat(chr($pad), $pad);
		
		# create a random IV to use with CBC encoding
		$iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
		$encData = mcrypt_encrypt(MCRYPT_3DES, $key, $inputConv, MCRYPT_MODE_CBC, $iv);
		
        return base64_encode($iv. $encData);
    }
    else{
        // no encryption
        if(!get_magic_quotes_gpc()){
            // System does not use add magic quotes
            return($input);
        }
        else{
            return stripslashes($input);
        }
    }
}

/**
 * Decrypt a string using php mcrypt functions
 * @global SSP_Configure $SSP_Config
 * @param string $input string to be encrypted
 * @param bool $useEncryption - override encryption locally
 * @return string
 */
function SSP_decrypt($input, $useEncryption = true){
    $SSP_Config = Configuration::getConfiguration();
    if($useEncryption){
		// Generate a key from a hash and create a 40 character key
		$key = md5(utf8_encode($SSP_Config->encryptionString));
		$key .= substr($key, 0, 16);
		$key = pack('H*', $key);
		
		$input = base64_decode($input);
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
		$iv = substr($input, 0, $iv_size);
		
		$input = substr($input, $iv_size);
		
		$data = mcrypt_decrypt(MCRYPT_3DES, $key, $input, MCRYPT_MODE_CBC, $iv);
		$blockSize = mcrypt_get_block_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
		$len = strlen($data);
		$pad = ord($data[$len-1]);
        return substr($data, 0, strlen($data) - $pad);
    }
    else{
        return($input);
    }
}

/**
 * Padds an ip number with zeros to ipV6 size
 * ie. five numbers in each section.
 * @param string $ipNumber
 * @return string
 */
function SSP_paddIp($ipNumber){
    // check for IPv6
	if(strpos($ipNumber, ":") !== false){
		$result = $ipNumber;
	}
	else{
		// break up IP number
		$numbers = explode(".",trim($ipNumber));
		foreach($numbers as $key => $value){
			$numbers[$key]=str_pad($value, 3, "0", STR_PAD_LEFT);
		}
		$result = implode(".",$numbers);
	}
    return($result);
}

/**
 * Trims the ip number down to the required accuracy for checking
 * @param string $ipNumber
 * @return string
 */
function SSP_trimIp($ipNumber){
    $SSP_Config = Configuration::getConfiguration();
	if(strpos($ipNumber, ":") !== false){
		$ipv6 = true;
	}
	else{
		$ipv6 = false;
	}
    $ip = SSP_paddIp($ipNumber);
	if(!$ipv6){
		// -ve last char removes last x chars
		$trim = ($SSP_Config->checkIpAccuracy - 4) * 4;
		return substr($ip, 0, $trim);
	}
	else{
		$trim = $SSP_Config->checkIpv6Accuracy * 5;
		return $ipNumber;
	}
}


/**
 * Cleans up any old tokens
 * @global SSP_Configure $SSP_Config
 * @global type $SSP_DB
 */
function SSP_CleanToken(){
    $SSP_Config = Configuration::getConfiguration();
	$SSP_DB = SspDb::getConnection();;

    $query="delete from ".$SSP_Config->tokenTable." where ". $SSP_DB->qt("time"). " < ?";
    $values = array((time()-$SSP_Config->tokenClean));
    $SSP_DB->query($query, $values, "SSP Functions: Cleaning up old general tokens");
}

/**
 * Generates a token for a form submission, returns with the hidden field code
 * @global SSP_Configure $SSP_Config
 * @global SSP_DB $SSP_DB
 * @param string $id - id to be used for 
 * @return string - token
 */
function SSP_Token($id){
    $SSP_Config = Configuration::getConfiguration();
	$SSP_DB = SspDb::getConnection();;

    // generate the token
    $token = md5(uniqid($SSP_Config->magicToken,true));

    // insert token into database
    $values = array(
    	"token" => $token,
    	"time" => time(),
    	"id" => $id,
    );
    $SSP_DB->insert($SSP_Config->tokenTable, $values, "SSP Functions: Generating a token");

    // return form field
    return($token);
}

/**
 * Checks that the token supplied by the form is valid
 * @param string $token - token to be checked
 * @param string $id - id of form from which the token comes
 * @return bool - true on match
 */
function SSP_TokenCheck($token, $id){
    $SSP_Config = Configuration::getConfiguration();
	$SSP_DB = SspDb::getConnection();;

    $tokenOk = false;

	// check is hex token
	$check = new \w34u\ssp\CheckData();
	if($check->check('hex', $token) !== 0){
		return false;
	}
	
    SSP_CleanToken();

    // Form token field exists
    $where = array("token" => $token, "id" => $id);
    $SSP_DB->delete($SSP_Config->tokenTable, $where, "SSP Functions: Deleting token");
    if($SSP_DB->affectedRows()){
        // token found and deleted
        $tokenOk = true;
    }
    return($tokenOk);
}

/**
 * Get path for the current page
 * @global SSP_Configure $SSP_Config - ssp configuration object
 * @param bool $withParams - get any parameters as well
 * @param bool $forceSSLPath - make use of https regardless
 * @return string - path
 */
function SSP_Path($withParams=false, $forceSSLPath=false){
    // returns the path to the current admin script

    $SSP_Config = Configuration::getConfiguration();

    $script = $_SERVER['REQUEST_URI'];
    if(false and $withParams and isset($_SERVER['QUERY_STRING']) and trim($_SERVER['QUERY_STRING']) != ""){
        $script .= "?".$_SERVER['QUERY_STRING'];
    }
    $host = $SSP_Config->url;
	$isSslResult = isset($_SERVER['HTTPS']);
	if($isSslResult){
		$sslResult = $_SERVER['HTTPS'];
	}
	$useSSL = false;
	if($isSslResult and $sslResult != "off"){
		// apache etc return a non empty value, iis returns off for no https
		$useSSL = true;
	}
    if($useSSL or $SSP_Config->useSSL or $forceSSLPath){
        $protocol='https://';
    }
    else{
        $protocol='http://';
    }
    return($protocol.$host.$script);
}

/**
 * Generates the domain start to be added to a path relative to the base
 * @global SSP_Configure $SSP_Config
 * @return string
 */
function SSP_Domain(){

    $SSP_Config = Configuration::getConfiguration();

    if($SSP_Config->useSSL){
        // if ssl already uses full path
        return("");
    }
    else{
        return("http://". $SSP_Config->url);
    }
}

/**
 * Send an email using php mail
 * @param string $fromName - who the email is from
 * @param string $fromAddress - email of who sent it
 * @param string $toName - Name of person recieving the email
 * @param string $toAddress - address to send the email to
 * @param string $subject - email subject in plain text
 * @param string $plainMessage - plain text message
 * @param string $htmlMessage - html version of the message
 * @param string $charset - character encoding
 * @return bool - true on success
 */
function SSP_SendMail($fromName, $fromAddress, $toName, $toAddress, $subject, $plainMessage, $htmlMessage = null, $charset="utf-8"){
	$subject = '=?' . $charset . '?B?' . base64_encode(mb_ereg_replace("[\r\n]", '', $subject)) . '?=';
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "X-Priority: 3\r\n";
	$headers .= "X-MSMail-Priority: Normal\r\n";
	$headers .= "X-Mailer: php/". phpversion(). "\r\n";
	$headers .= "From: \"". strip_tags($fromName). "\" <". strip_tags($fromAddress). ">\r\n";
	$headers .= 'Reply-To: ' .strip_tags($fromAddress) . "\r\n";
	$toAddressExtended = '"'. strip_tags($toName). '" <'. strip_tags($toAddress). '>';
	if($htmlMessage === null){
		$headers .= "Content-type: text/plain; charset=". strip_tags($charset). "\r\n";
		$message = $plainMessage;
	}
	else{
		$boundary = uniqid('np');
		$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
		$message = $plainMessage;
		$message .= "\r\n\r\n--" . $boundary . "\r\n";
		$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
		$message .= $htmlMessage;
		$message .= "\r\n\r\n--" . $boundary . "\r\n";
	}
	// check for spam
	if (stristr($plainMessage,'Content-Type:') or stristr($plainMessage,'bcc:') or stristr($htmlMessage,'Content-Type:') or stristr($htmlMessage,'bcc:')) {
		SSP_error('SSP_SendMail: failed to send email due to spam content in message to'. $toAddress, E_USER_WARNING);
		return(false);
	}
	return mail($toAddressExtended, $subject, $message, $headers);
}

/**
 * 
 * @global SSP_Configure $SSP_Config
 * @global SSP_DB $SSP_DB
 * @param string $userId
 * @param int $time - time in seconds
 * @return string - token 32v chars
 */
function SSP_ResponseToken($userId, $time){
    $SSP_Config = Configuration::getConfiguration();
	$SSP_DB = SspDb::getConnection();;

    // generate the token
    $token = md5(uniqid($SSP_Config->magicToken,true));

    // insert token into database
	$fields = array(
					"token" => $token,
					"time" => (time()+$time),
					"UserId" => $userId,
					);
	$SSP_DB->insert($SSP_Config->responseTable, $fields, "SSP Functions: Issuing a response token");

    // return form field
    return($token);
}

/**
 * Checks that the supplied token still exists and has not timed out.
 * @global SSP_Configure $SSP_Config
 * @global type $SSP_DB
 * @param string $token
 * @return string/bool - UserId or false on not found
 */
function SSP_CheckResponseToken($token){
    $SSP_Config = Configuration::getConfiguration();
	$SSP_DB = SspDb::getConnection();;

    $tokenOk = false;
	
	$check = new \w34u\ssp\CheckData();
	if($check->check('hex', $token) !== 0){
		return false;
	}

    // Form token field exists
	$where = array("token"=>$token);
	$row = $SSP_DB->get($SSP_Config->responseTable, $where, "SSP Functions: Finding current form token");
    if($SSP_DB->numRows()){
        if($row->time >= time()){
            $tokenOk = $row->UserId;
        }
		$SSP_DB->delete($SSP_Config->responseTable, $where, "SSP Functions: Removing current form token");
    }
    return($tokenOk);
}

/**
 * Cleans up any old response tokens
 * @global SSP_Configure $SSP_Config
 * @global type $SSP_DB
 */
function SSP_ResponseClean(){
    $SSP_Config = Configuration::getConfiguration();
	$SSP_DB = SspDb::getConnection();;

    $query="delete from ".$SSP_Config->responseTable." where ". $SSP_DB->qt("time"). " < ?";
    $values = array(time());
    $SSP_DB->query($query, $values, "SSP Functions: Cleaning up old response tokens");
}

/**
 * General file download routine
 * @param string $fileName
 * @param string $filePath
 * @param string $downloadName
 * @param string $type - type of download
 */
function SSP_FileDownload($fileName, $filePath, $downloadName="", $type="application/octet-stream"){
    $theFile = $filePath.$fileName;
    if($downloadName != ""){
        $download=$downloadName;
    }
    else{
        $download=$fileName;
    }
	if(file_exists($theFile)){
       header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
       header ("Content-Type: $type");
       header ("Content-Length: " . filesize($theFile));
       header ("Content-Disposition: attachment; filename=$download");
       readfile($theFile);
	}
	else{
		echo 'No file';
	}
}

/**
 * Diverts to a path.
 * Default template has the following fields:
 * pageTitle - title seen in browser
 * explanation - what is happening ie. diverting
 * linkText - text is backup link text
 * linkPath - path for link to routine, also used in header
 * $content = array("pageTitle" => "", "explanation" => "", "linkText" => "")
 * 
 * @global SSP_Configure $SSP_Config
 * @param string $path
 * @param string $content - additional content for divert template
 * @param string $template - path to a template to use instead of default
 * @param bool $divert - do automatic divert else shows the divert page
 */
function SSP_Divert($path, $content="", $template="", $divert=true){
    $SSP_Config = Configuration::getConfiguration();

    // generate page for auto diversion failure or if auto diversion not used
    if($template == ""){
        $templatePath = $SSP_Config->divertTemplate;
    }
    else{
        $templatePath = $template;
    }

    if(is_Array($content)){
        $templateContent = $content;
    }
    else{
        $templateContent = $SSP_Config->divertDefaultContent;
    }

    // add divert path into page content
    $templateContent["linkPath"] = $path;

    // check for SID being passed by parameter, ie. cookie is not set
    if(trim(SID) != ""){
    	// check for other parameters
    	if(strpos($path, '?')){
    		$divertPath = $path. '&'. SID;
    	}
    	else{
    		$divertPath = $path. '?'. SID;
    	}
    }
    else{
    	$divertPath = $path;
    }

    // issue divert header if required and no debug
    if($divert and !$SSP_Config->divertDebug){
        header("Location: ".$divertPath);
    }
    $page = new Template($templateContent, $templatePath);
    echo $page->output();

    // prevent any further page output
    exit();
}

/**
 *  Gets get and/or post parameters
 *	@param string $paramName - name of parameter to get
 *	@param any $default - default value if parmeter not found
 *	@param bool $getParam - get parameter has priority
 * @return any Get or post variable value
 */
function SSP_getParam($paramName, $default, $getParam=true, $dataType="lable"){
	
	$checkData = new CheckData();
	
	if($getParam){
		if(isset($_GET[$paramName])){
			$result = $_GET[$paramName];
		}
		elseif(isset($_POST[$paramName])){
			$result = $_POST[$paramName];
		}
		else{
			$result = $default;
		}
	}
	else{
		if(isset($_POST[$paramName])){
			$result = $_POST[$paramName];
		}
		elseif(isset($_GET[$paramName])){
			$result = $_GET[$paramName];
		}
		else{
			$result = $default;
		}
	}
	
	if($checkData->check($dataType, $result)){
		$result = $default;
	}
	
	return($result);
}

/**
 * Changes get and/or post parameters
 * @param any $param
 * @param string $paramName
 * @param bool $getParam - look in get vars first
 * @param string $dataType - data type to check
 */
function SSP_changeParam(&$param, $paramName, $getParam=true, $dataType="lable"){
	$checkData = new CheckData();
	
	$result = "";
	$gotChange = false;
	if($getParam){
		if(isset($_GET[$paramName])){
			$result = $_GET[$paramName];
			$gotChange = true;
		}
		elseif(isset($_POST[$paramName])){
			$result = $_POST[$paramName];
			$gotChange = true;
		}
	}
	else{
		if(isset($_POST[$paramName])){
			$result = $_POST[$paramName];
			$gotChange = true;
		}
		elseif(isset($_GET[$paramName])){
			$result = $_GET[$paramName];
			$gotChange = true;
		}
	}
	if($gotChange and !$checkData->check($dataType, $result)){
		$param = $result;
	}
}

/**
 * Convert MySql time stamp to unix time stamp
 * @param string $dbTimeStamp
 * @return string
 */
function SSP_dbTimeStampToTime($dbTimeStamp){
	$spacePos = strpos(trim($dbTimeStamp), " ");
	$date = substr($dbTimeStamp, 0, $spacePos);
	$time = substr($dbTimeStamp, ($spacePos+1));
	$dateArray = explode("-",$date);
	$timeArray = explode(":", $time);
	$timeStamp = mktime($timeArray[0], $timeArray[1], $timeArray[2], $dateArray[1], $dateArray[2], $dateArray[0]);
	return($timeStamp);
}

/**
 * Converts a mysql date to unix timestamp
 * @param string $dbDate
 * @return string
 */
function SSP_dbDateToTime($dbDate){
	$dateArray = explode("-",$dbDate);
	$timeStamp = mktime(12, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]);
	return($timeStamp);
}

/**
 * Dump a peice of data in a nice display format
 * @param any $var
 * @param bool $display - send to stdOut
 * @param bool $die die after output
 * @return string
 */
function SSP_Dump($var, $display=true, $die = false){
	$result = "<pre>".
	print_r($var, true).
	"</pre>";
	if($display){
		echo $result;
	}
	if($die){
		die();
	}
	return($result);
}

/**
 * Display an error, save to log and email to admins
 * @global SSP_Configure $SSP_Config
 * @param int $errno
 * @param string $errstr - error generated
 * @param string $errfile - file in whioch the error occurs
 * @param int $errline
 */
function SSP_errorHandler($errno, $errstr, $errfile, $errline){
	/* @var $SSP_Config SSP_Configuration */
    $SSP_Config = Configuration::getConfiguration();
	$error = SSP_error($errstr, $errno, false);
	if($SSP_Config->displayNoticesWarnings){
		echo '<pre>';
		echo $error;
		echo '</pre>';
	}
	else{
		error_log($error, $SSP_Config->message_type, $SSP_Config->errorLog, $SSP_Config->adminEmail);
		foreach($SSP_Config->errorAdmins as $toAddress => $toName){
			SSP_SendMail("SSP error handler", $SSP_Config->noReplyEmail, $toName, $toAddress, "Notice or warning on ". $SSP_Config->siteName, $error);
		}
	}
}

/**
 * Log a text to log file
 * @global SSP_Configure $SSP_Config
 * @param string $text - text to be logged
 */
function SSP_log($text){
	/* @var $SSP_Config SSP_Configuration */
    $SSP_Config = Configuration::getConfiguration();
	error_log($text, $SSP_Config->message_type, $SSP_Config->errorLog, $SSP_Config->adminEmail);
}

/**
 * Trigger a user defined error
 * @param string $error error thrown
 * @param integer $errorType User error type, e.g. E_USER_WARNING, E_USER_NOTICE and E_USER_ERROR
 * @param bool $triggerError - trigger an error
 */
function SSP_error($error, $errorType = E_USER_WARNING, $triggerError=true){
	$SSP_Config = Configuration::getConfiguration();
	if($SSP_Config->errorDisplayBacktrace){
		$error .= "\nDebug backtrace\n";
		ob_start();
		debug_print_backtrace();
		$error .= ob_get_clean();
	}
	if($triggerError){
		trigger_error($error, $errorType);
	}
	return($error);
}

/**
 * Limits a string to a number of characters, truncates at a space
 * between words. Removes all tags.
 * @param string $string - string to be truncated
 * @param integer $width - number of chars to which to break
 * @param integer $lines - number of lines to produce
 * @return string - truncated string with tags removed.
 */
function ssp_stringTruncate($string, $width, $lines){

	$breakCharacters = "[br]";
	$result = html_entity_decode(strip_tags($string));
	if(strlen($result) > ($width * $lines)){
		$result = wordwrap($result, $width, $breakCharacters);
		$offset = 0;
		for($i=1; $i <= $lines; $i++){
			$breakPosition = strpos($result, $breakCharacters, $offset);
			$offset = $breakPosition + 1;
		}
		$result = str_replace($breakCharacters, ' ', substr($result, 0, $breakPosition));
	}
	return($result);
}

/**
 * store a veriable value in the session variables
 * @param string $sessionVarName - storage name in the session variables
 * @param any $default - defualt value if storage does not initially exist
 * @return any
 */
function &SSP_attachToSeshVar($sessionVarName, $default){
	if(!isset($_SESSION[$sessionVarName])){
		$_SESSION[$sessionVarName] = $default;
	}
	$refVar =& $_SESSION[$sessionVarName];
	return($refVar);
}

/**
 * Get a twiiter feed and return as a string
 * @param string $userName - twitter feed user name
 * @param int $recoverNumber - number of tweets to recover
 * @return string - formatted string of tweets
 */
function SSP_twitterFeed($userName, $recoverNumber){
    $username = $userName;
    $limit = $recoverNumber;
    $feed = 'http://twitter.com/statuses/user_timeline.rss?screen_name='.$username.'&count='.$limit;
    $tweets = @file_get_contents($feed);

	if($tweets){
	$tweets = str_replace("&", "&", $tweets);
	$tweets = str_replace("<", "<", $tweets);
	$tweets = str_replace(">", ">", $tweets);
	$tweet = explode("<item>", $tweets);
    $tcount = count($tweet) - 1;

	for ($i = 1; $i <= $tcount; $i++) {
		$endtweet = explode("</item>", $tweet[$i]);
		$title = explode("<title>", $endtweet[0]);
		$content = explode("</title>", $title[1]);
		$content[0] = str_replace("&#8211;", "&mdash;", $content[0]);

		$content[0] = preg_replace("/(http:\/\/|(www\.))(([^\s<]{4,68})[^\s<]*)/", '<a href="http://$2$3" target="_blank">$1$2$4</a>', $content[0]);
		$content[0] = str_replace("$username: ", "", $content[0]);
		$content[0] = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $content[0]);
		$content[0] = preg_replace("/ #(\w+)/", " <a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $content[0]);
		$mytweets[] = $content[0];
	}

	$tweetout = "";
	while (list(, $v) = each($mytweets)) {
		$tweetout .= "<p>$v</p>\n";
	}
	}
	else{
		$tweetout = "";
	}
	return($tweetout);
}

/**
 * Detect and ajax call to a routine
 * @return boolean - true on ajax call
 */
function SSP_isAjaxCall(){
	$ajaxCall = false;
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
		$ajaxCall = true;
	}
	return $ajaxCall;
}
/* End of file functions.php */
/* Location: ./src/functions.php */