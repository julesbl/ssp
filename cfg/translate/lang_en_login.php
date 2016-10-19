<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	lang_en_login.php
*   Created:	22/02/2013
*   Descrip:	Add translation strings for basic login and protection.
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
*   Rev. Date	3/05/2013
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	19-Jan-2016
*   Descrip:	Changed for composer.
*/
w34u\ssp\Translate::addToLanguage('en', array(
	// protection object strings
	'Invalid page access level' => 'Invalid page access level',
	'parameter pageCaching should be of type bool' => 'parameter pageCaching should be of type bool',
	'parameter pageValid should be of type int' => 'parameter pageValid should be of type int',
	'parameter pageCheckEquals should be of type bool' => 'parameter pageCheckEquals should be of type bool',
	'Incorrect SSP_Protection parameters' => 'Incorrect SSP_Protection parameters',
	'Diverting to logon' => 'Diverting to logon',
	'General protection fault' => 'General protection fault',
	'Click here to login if divert does not happen within 5 seconds' => 'Click here to login if divert does not happen within 5 seconds',
	'Access control fault' => 'Access control fault',
	// login screen texts
	'Email or password incorrect' => 'Email or password incorrect',
	'User name or password incorrect' => 'User name or password incorrect',
	'Your email' => 'Your email',
	'Your user name' => 'Your user name',
	'Password' => 'Password',
	'Remember me (do not tick this box on a public computer)' => 'Remember me (do not tick this box on a public computer)',
	// logged off screen title
	'Logged off' => 'Logged off',
	// login success page
	'Logon Success' => 'Logon Success',
	'Welcome Back' => 'Welcome Back',
	// password recovery
	'Password recovery' => 'Password recovery',
	'Enter your registered email' => 'Enter your registered email',
	'Recover Password' => 'Recover Password',
	'New password succesfully entered' => 'New password succesfully entered',
	'Invalid recovery link, please check you have used the correct recovery email, if problems persist please contact site admin' => 'Invalid recovery link, please check you have used the correct recovery email, if problems persist please contact site admin',
	'Invalid recovery link, please check the email supplied and ensure you enter the whole of the url supplied, if problems persist please contact site admin' => 'Invalid recovery link, please check the email supplied and ensure you enter the whole of the url supplied, if problems persist please contact site admin',
	// join up form
	'First name' => 'First name',
	'Last name' => 'Last name',
	'Your email' => 'Your email',
	'Your password' => 'Your password',
	'Enter password again' => 'Enter password again',
	// user confirmation
	"User Confirm Failure<br />Invalid User" => "User Confirm Failure<br />Invalid User",
	"User Confirm Failure<br />Invalid token" => "User Confirm Failure<br />Invalid token",
));
/* End of file lang_fr.php */
/* Location: /translate/ */