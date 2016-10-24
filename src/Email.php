<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	Email.php
*   Created:	25-July-2011
*   Descrip:	Send text emails using a standard template.
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
*   Rev. Date	25-July-2011
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	14/01/2016
*   Descrip:	Composer implemented.
*/

namespace w34u\ssp;

class Email{
	
	/** 
	 * configuration object
	 * @var \w34u\ssp\Configuration
	 */
	private $cfg;
	/** 
	 *  email template name
	 * @var string
	 */
	public $emailTemplate = "emailTemplateMain.tpl";
	/** 
	 *  charcter set to be
	 * @var string
	 */
	private $charset = "UTF-8";
	/**
	 * Name of function to send and email
	 * @var string
	 */
	private static $emailRoutine = '\w34u\ssp\SSP_SendMail';

	/**
	 * Constructor
	 */
	public function __construct(){
		$this->cfg = \w34u\ssp\Configuration::getConfiguration();
		$this->charset = $this->cfg->siteEncoding;
	}
	
	/**
	 * Send a general email
	 * @param array/object $emailContent - content for email template
	 * @param string $emailTpl - name of email template
	 * @param string $fromEmail - from email address
	 * @param string $fromName - from name
	 * @param string $toEmail - recipient email address
	 * @param string $toName - recipient name
	 * @return bool - true on success
	 */
	public function generalEmail($emailContent, $emailTpl, $fromEmail, $fromName, $toEmail, $toName){
		$emailBody = new Template($emailContent, $emailTpl);
		$emailBody->encode = false;
		// first two lines are comment and subject of email
		$emailBody->numberReturnLines = 2;
		$emailContent['content'] = $emailBody->output();
		$emailContent['domain'] = $this->cfg->url;
		$emailContent['adminEmail'] = $this->cfg->adminEmail;
		$subject = $emailBody->returnedLines[1];
		$subject = '=?'. $this->charset. '?B?'.base64_encode(mb_ereg_replace("[\r\n]", '', $subject)).'?=';
		$tpl = new Template($emailContent, $this->emailTemplate);
		$tpl->encode = false;
		$tpl->numberReturnLines = 1; // remove comment from the top
		$message = $tpl->output();
		$result = $this->ECRIAmailer($fromName, $fromEmail, $toName, $toEmail, $subject, $message, $this->charset);
		return($result);
	}
	
	/**
	 * Send emails to the admin error recipients
	 * @param array $emailContent - replacement fields for the email
	 * @param string $emailTpl - template to be used
	 */
	public function adminErrorEmail($emailContent, $emailTpl){
		foreach($this->cfg->errorAdmins as $email => $name){
			$result = $this->generalEmail($emailContent, $emailTpl, 
					$this->cfg->noReplyEmail, $this->cfg->noReplyName, $email, $name);
		}
		return($result);
	}
	
	/**
	 * Sends an email from no-reply
	 * @param array $emailContent
	 * @param string $emailTpl
	 * @param string $toEmail
	 * @param string $toName
	 * @return bool - true on success 
	 */
	public function noReplyEmail($emailContent, $emailTpl, $toEmail, $toName){
		$result = $this->generalEmail($emailContent, $emailTpl, 
					$this->cfg->noReplyEmail, $this->cfg->noReplyName, $toEmail, $toName);
		return($result);
	}

	/**
	 * Send an email with checks for injection
	 * @param string $fromName
	 * @param string $fromAddress
	 * @param string $toName
	 * @param string $toAddress
	 * @param string $subject
	 * @param string $message
	 * @return bool 
	 */
	private function ECRIAmailer($fromName, $fromAddress, $toName, $toAddress, $subject, $message, $charset="utf-8"){
		// Copyright 2005 ECRIA LLC, http://www.ECRIA.com
		// Please use or modify for any purpose but leave this notice unchanged.
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset={$charset}\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: Normal\n";
		$headers .= "X-Mailer: php/". phpversion(). "\n";
		$headers .= "From: \"". $fromName. "\" <". $fromAddress. ">\n";
		$headers .= 'Reply-To: ' .$fromAddress . "\n";
		$toAddressExtended = '"'. $toName. '" <'. $toAddress. '>';
		// check for spam
		if (stristr($message,'Content-Type:') || stristr($message,'bcc:')) {
			return(false);
		}
		else{
			return call_user_func(self::$emailRoutine, $toAddressExtended, $subject, $message, $headers);
		}
	}
	
	/**
	 * Change email routine used to send email
	 * @param string $emailRoutine - email routine to send email
	 */
	public static function setEmailFunction($emailRoutine){
		self::$emailRoutine = $emailRoutine;
	}
}
/* End of file Email.php */
/* Location: ./src/Email.php */