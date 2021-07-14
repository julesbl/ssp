<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	lang_en_admin.php
*   Created:	22/02/2013
*   Descrip:	Add translation strings for administration.
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
*   Rev. Date	21/05/2013
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	19-Jan-2016
*   Descrip:	Changed for composer.
*/
w34u\ssp\Translate::addToLanguage('en', array(
	// genearl site stuff
	'SSP Development' => 'SSP Development',
	'Member admin' => 'Member admin',
	'Advanced information' => 'Advanced information',
	'User information' => 'User information',
	'New user' => 'New user',
	// Menu at the top of admin
	'User Details' => 'User Details',
	'New User' => 'New User',
	'List Users' => 'List Users',
	'Home' => 'Home',
	'Log off' => 'Log off',
	// side menu - edit user information
	'Change Info' => 'Change Info',
	'Change user information' => 'Change user information',
	'Change Password' => 'Change Password',
	'Change user password' => 'Change user password',
	'Change Email' => 'Change Email',
	'Change user email' => 'Change user email',
	'Basic Info' => 'Basic Info',
	'Show basic information' => 'Show basic information',
	'Advanced Info' => 'Advanced Info',
	'Show advanced information' => 'Show advanced information',
	'Change Advanced' => 'Change Advanced',
	'Change advanced information' => 'Change advanced information',
	'Send Joining Email' => 'Send Joining Email',
	'Send a joinup email to the user' => 'Send a joinup email to the user',
	'Email Member' => 'Email Member',
	'Email the member' => 'Email the member',
	'Help' => 'Help',
	// forms - edit user information
	'Title (Mr/Mrs/Mz/Dr/Prof.)' => 'Title (Mr/Mrs/Mz/Dr/Prof.)',
	'First Name' => 'First Name',
	'Initials' => 'Initials',
	'Family  Name' => 'Family  Name',
	'Address' => 'Address',
	'Town or City' => 'Town or City',
	'Post Code' => 'Post Code',
	'County' => 'County',
	'Next' => 'Next',
	'Save' => 'Save',
	// change password form
	'Your original Password' => 'Your original Password',
	'New password' => 'New password',
	'Enter new password again' => 'Enter new password again',
	'Both passwords must be the same' => 'Both passwords must be the same',
	'Invalid original password' => 'Invalid orriginal password',
	// change email form
	'Your password' => 'Your password',
	'New email' => 'New email',
	'Save new email' => 'Save new email',
	'Invalid password' => 'Invalid password',
	'Email is already in use.' => 'Email is already in use.',
	// Change advanced information
	'Change advanced information' => 'Change advanced information',
	'Check user IP for logon and session' => 'Check user IP for logon and session',
	'User IP address' => 'User IP address list, comma seperated',
	'User Access rights' => 'User Access rights',
	'Enable two factor authentication' => 'Enable two factor authentication',
	'User Disabled' => 'User Disabled',
	'User Pending program enable' => 'User Pending program enable',
	'User waiting admin vetting' => 'User waiting admin vetting',
	'User creation finished' => 'User creation finished',
	'Waiting for user to act on email' => 'Waiting for user to act on email',
	// send join up email
	'Send joining email' => 'Send joining email',
	'Send joinup email to this user?' => 'Send joinup email to this user?',
	// send email to the user
	'Email member' => 'Email member',
	'Subject' => 'Subject',
	'Message' => 'Message',
	// password recovery
	'Password recovery' => 'Password recovery',
	'Enter your registered email' => 'Enter your registered email',
	'Recover Password' => 'Recover Password',
	// join up
	'Your password' => 'Your password',
	'Enter password again' => 'Enter password again',
	'Type of membership' => 'Type of membership',
	// side menu - list users
	'Modify Search' => 'Modify Search',
	'List Admin Pending' => 'List Admin Pending',
	'Defualt Listing' => 'Defualt Listing',
	// Search users form and page
	'All' => 'All',
	'Any' => 'Any',
	'Modify search criteria' => 'Modify search criteria',
	'Search' => 'Search',
	'for' => 'for',
	'Add Search field' => 'Add Search field',
	'Results per page' => 'Results per page',
	'Member Access' => 'Member Access',
		// translation for member list dropdown
		'All Types' => 'All Types',
		'Standard User' => 'Standard User',
		'Enhanced User' => 'Enhanced User',
		'Administrator' => 'Administrator',
	'Filter using flags' => 'Filter using flags',
	'Users who have been disabled' => 'Users who have been disabled',
	' false' => ' false',
	' true' => ' true',
	' ignore' => ' ignore',
	'User who are waiting for external OK' => 'User who are waiting for external OK',
	'User Admin Pending' => 'User Admin Pending',
	'User Properly created' => 'User Properly created',
	'Waiting for user to respond to email' => 'Waiting for user to respond to email',
	'Search Now' => 'Search Now',
	'Reset Search Criteria' => 'Reset Search Criteria',
	// List translations
	'all a b c d e f g h i j k l m n o p q r s t u v w x y z' => 'all a b c d e f g h i j k l m n o p q r s t u v w x y z',
	// the next item is used to detect that all has been selected from the list for generating the query
	'all' => 'all',
	'User fault' => 'User flag fault',
	// Site creation and intialisation
	'Site database creation and intialisation' => 'Site database creation and intialisation',
	'Admin email' => 'Admin email',
	'Admin user name' => 'Admin user name',
	'Repeat the password' => 'Repeat the password',
	'Please check the passwords, they must be the same' => 'Please check the passwords, they must be the same',
	'There are already admin users in the system, please delete these first if attempting to recover access to the system.' => 'There are already admin users in the system, please delete these first if attempting to recover access to the system.',
	'Admin user created' => 'Admin user created',
));
/* End of file lang_fr.php */
/* Location: /translate/ */