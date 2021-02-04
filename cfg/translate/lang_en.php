<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	lang_en.php
*   Created:	22/02/2013
*   Descrip:	Setup the translation strings native english translation.
*
*   Copyright 2005-2013 Julian Blundell, w34u
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
*   Rev. Date	22/02/2013
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	19-Jan-2016
*   Descrip:	Changed for composer.
*/
w34u\ssp\Translate::setupStrings('en', array(
	// strings for SSP_datacheck
	'alphanumeric characters, CR, TAB, \', ", +, -, _ space comma () / ! [] and ?' => 'alphanumeric characters, CR, TAB, \', ", +, -, _ space comma () / ! [] and ?',
	'0 to 9, a to z, A to Z' => '0 to 9, a to z, A to Z',
	'0 to 9, / space comma, a to z, A to Z' => '0 to 9, / space comma, a to z, A to Z',
	'0 to 9, :' => '0 to 9, :',
	'0 to 9, () + - . space' => '0 to 9, () + - . space',
	'0 to 9, - ' => '0 to 9',
	'0 to 9, .- e +' => '0 to 9, .- e +',
	'0 to 9, a to z, A to Z' => '0 to 9, a to z, A to Z',
	'0 to 7' => '0 to 7',
	'0 to 9, a to z, A to Z, .-_+ @' => '0 to 9, a to z, A to Z, .-_+ @',
	'0 to 9, a to z, A to Z, .-_+ # / : ? % ~ [] @ ! $ = ; , &' => '0 to 9, a to z, A to Z, .-_+ # / : ? % ~ [] @ ! $ = ; , &',
	'0 to 9, a to z, A to Z, -_' => '0 to 9, a to z, A to Z, -_',
	
	// form error messages
	'Duplicate element name %s in this form for data table %s' => 'Duplicate element name %s in this form for data table %s',
	'The form has expired, please submit the form again' => 'The form has expired, please submit the form again',
	'Token data type incorrect, possible hack attempt, data:%s:' => 'Token data type incorrect, possible hack attempt, data:%s:',
	'Possible hack attempt, hidden field %s came back with wrong data type, data:%s:' => 'Possible hack attempt, hidden field %s came back with wrong data type, data:%s:',
	'%s has a problem, %s' => '%s has a problem, %s',
	'invalid characters have been entered, valid characters are: %s, please modify your entry to reflect this. Thanks!' => 'invalid characters have been entered, valid characters are: %s, please modify your entry to reflect this. Thanks!',
	'the URL is too long' => 'the URL is too long',
	'the URL needs a period' => 'the URL needs a period',
	'the URL does not exist' => 'the URL does not exist',
	'this Email is too long, please check and shorten it. Thanks!' => 'this Email is too long, please check and shorten it. Thanks!',
	'this email needs an @ and a period, please check and add them. Thanks!' => 'this email needs an @ and a period, please check and add them. Thanks!',
	'Invalid characters have been entered, valid characters are: %s, please modify your entry to reflect this. Thanks!' => 'Invalid characters have been entered, valid characters are: %s, please modify your entry to reflect this. Thanks!',
	'This URL is too long' => 'This URL is too long',
	'This URL needs a period' => 'This URL needs a period',
	'This URL does not exist' => 'This URL does not exist',
	'This Email is too long, please check and shorten it. Thanks!' => 'This Email is too long, please check and shorten it. Thanks!',
	'This email needs an @ and a period, please check and add them. Thanks!' => 'This email needs an @ and a period, please check and add them. Thanks!',
	'Invalid element type: %s, element name: %s' => 'Invalid element type: %s, element name: %s',
	'%s is a required field, please enter a value. Thanks!"' => '%s is a required field, please enter a value. Thanks!"',
	'Please ensure that %s has less than %s characters. Thanks!' => 'Please ensure that %s has less than %s characters. Thanks!',
	'Please ensure that %s has at least %s characters. Thanks!' => 'Please ensure that %s has at least %s characters. Thanks!',
	'Value returned by %s not in valid results' => 'Value returned by %s not in valid results',
	'This is a required field, please enter a value. Thanks!' => 'This is a required field, please enter a value. Thanks!',
	'Please ensure that this has less than %2$s characters. Thanks!' => 'Please ensure that this has less than %2$s characters. Thanks!',
	'Please ensure that this has at least %2$s characters. Thanks!' => 'Please ensure that this has at least %2$s characters. Thanks!',
	'Value returned by this is not in valid results' => 'Value returned by this is not in valid results',
	'%s *' => '%s *', // required field text added to label
	// file upload errors
	'The uploaded file exceeds the upload_max_filesize directive in php.ini' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
	'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form' => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
	'The uploaded file was only partially uploaded' => 'The uploaded file was only partially uploaded',
	'No file was uploaded' => 'No file was uploaded',
	'Failed to write file to disk' => 'Failed to write file to disk',
	'A PHP extension stopped the file upload' => 'A PHP extension stopped the file upload',
	'Unknown file upload error' => 'Unknown file upload error',
	'Invalid file type ' => 'Invalid file type ',
	'Failed to move file' => 'Failed to move file',
));
/* End of file lang_fr.php */
/* Location: /translate/ */