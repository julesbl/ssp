<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	SSP_email.php
*   Created:	25-July-2011
*   Descrip:	Send text emails using a standard template.
*
*   Copyright 2005-2011 Julian Blundell, w34u
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
*   Rev. Date	25-July-2011
*   Descrip:	Created.
*/

class SSP_email{
	
	/** @var SSP_Configuration - configuration object */
	var $cfg;
	/** @var string - email template name */
	var $emailTemplate = "emailTemplateMain.tpl";
	/** @var string charcter set to be  */
	var $charset = "UTF-8";

	/**
	 * Constructor
	 * @param SSP_Configuration $cfg - configuration object
	 */
	public function __construct($cfg){
		$this->cfg = $cfg;
		$this->charset = $cfg->siteEncoding;
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
		$emailBody = new SSP_Template($emailContent, $emailTpl);
		$emailBody->encode = false;
		// first two lines are comment and subject of email
		$emailBody->numberReturnLines = 2;
		$emailContent['content'] = $emailBody->output();
		$emailContent['domain'] = $this->cfg->url;
		$emailContent['adminEmail'] = $this->cfg->adminEmail;
		$subject = $emailBody->returnedLines[1];
		$subject = '=?'. $this->charset. '?B?'.base64_encode(mb_ereg_replace("[\r\n]", '', $subject)).'?=';
		$tpl = new SSP_Template($emailContent, $this->emailTemplate);
		$tpl->encode = false;
		$tpl->numberReturnLines = 1; // remove comment from the top
		$message = $tpl->output();
		$result = ECRIAmailer($fromName, $fromEmail, $toName, $toEmail, $subject, $message, $this->charset);
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
}
/* End of file SSP_email.php */
/* Location: ./sspincludes/SSP_email.php */