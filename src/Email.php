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

class Email {

	/**
	 * configuration object
	 * @var \w34u\ssp\Configuration
	 */
	private $cfg;

	/**
	 * email template name
	 * @var string
	 */
	public $emailTemplate = "emailTemplateMain.tpl";
	
	/**
	 * email html template
	 * @var string
	 */
	public $emailTemplateHtml = "emailTemplateMainHtml.tpl";

	/**
	 * character set to be used in emails
	 * @var string
	 */
	private static $charset = "UTF-8";

	/**
	 * Name of function to send and email
	 * @var string
	 */
	private static $emailRoutine = '\w34u\ssp\SSP_SendMail';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->cfg = \w34u\ssp\Configuration::getConfiguration();
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
	public function generalEmail($emailContent, $emailTpl, $fromEmail, $fromName, $toEmail, $toName) {
		// Text email
		$emailBody = new Template($emailContent, $emailTpl);
		$emailBody->encode = false;
		// first two lines are comment and subject of email
		$emailBody->numberReturnLines = 2;
		$emailContent['content'] = $emailBody->output();
		$emailContent['domain'] = $this->cfg->url;
		$emailContent['adminEmail'] = $this->cfg->adminEmail;
		$subject = $emailBody->returnedLines[1];
		$tpl = new Template($emailContent, $this->emailTemplate);
		$tpl->encode = false;
		$tpl->numberReturnLines = 1; // remove comment from the top
		$message = $tpl->output();
		$htmlMessage = null;
		if($this->cfg->htmlEmails){
			// system html email templates always have the same name with Html.tpl on the end
			$emailTplHtml = substr($emailTpl, 0, -4). 'Html.tpl';
			$emailBodyHtml = new Template($emailContent, $emailTplHtml);
			$emailBodyHtml->numberReturnLines = 1;
			$emailContent['content'] = $emailBodyHtml->output();
			$tplHtml = new Template($emailContent, $this->emailTemplateHtml);
			$tplHtml->numberReturnLines = 1;
			$htmlMessage = $tplHtml->output();
		}
		$result = $this->sendmail($fromName, $fromEmail, $toName, $toEmail, $subject, $message, $htmlMessage);
		return($result);
	}

	/**
	 * Send emails to the admin error recipients
	 * @param array/object $emailContent - content for email template
	 * @param string $emailTpl - name of email template
	 * @return bool - true on success
	 */
	public function adminErrorEmail($emailContent, $emailTpl) {
		foreach ($this->cfg->errorAdmins as $email => $name) {
			$result = $this->generalEmail($emailContent, $emailTpl, $this->cfg->noReplyEmail, $this->cfg->noReplyName, $email, $name);
		}
		return($result);
	}

	/**
	 * Sends an email from no-reply
	 * @param array/object $emailContent - content for email template
	 * @param string $emailTpl - name of email template
	 * @param string $toEmail - recipient email address
	 * @param string $toName - recipient name
	 * @return bool - true on success
	 */
	public function noReplyEmail($emailContent, $emailTpl, $toEmail, $toName) {
		$result = $this->generalEmail($emailContent, $emailTpl, $this->cfg->noReplyEmail, $this->cfg->noReplyName, $toEmail, $toName);
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
	 * @param string $htmlMessage
	 * @param string $charset - charset to use
	 * @return bool
	 */
	private function sendmail($fromName, $fromAddress, $toName, $toAddress, $subject, $message, $htmlMessage, $charset = null) {
		if(empty($charset)){
			$charset = self::$charset;
		}
		return call_user_func(self::$emailRoutine, $fromName, $fromAddress, $toName, $toAddress, $subject, $message, $htmlMessage, $charset);
	}

	/**
	 * Set the character set to be used in the emails
	 * @param string $charset - Character set e.g. UTF-8
	 */
	public static function setEmailCharset($charset) {
		self::$charset = $charset;
	}

	/**
	 * Change email routine used to send email
	 * @param string $emailRoutine - email routine to send email
	 */
	public static function setEmailFunction($emailRoutine) {
		self::$emailRoutine = $emailRoutine;
	}

}
/* End of file Email.php */
/* Location: ./src/Email.php */