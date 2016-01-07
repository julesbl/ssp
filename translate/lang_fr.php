<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	lang_fr.change.php
*   Created:	22/02/2013
*   Descrip:	Setup the translation strings for native french translation.
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
*/
SSP_translate::setupStrings('fr', array(
	// strings for SSP_datacheck
	'alphanumeric characters, CR, TAB, \', ", +, -, _ space comma () / ! [] and ?' => 'caractères alphanumériques, CR, TAB, \ \', ", +, -, virgule espace _ () / [] et ?',
	'0 to 9, a to z, A to Z' => '0 à 9, a à z, A à Z',
	'0 to 9, / space comma, a to z, A to Z' => 'de 0 à 9, virgule espace /, z à un, un à Z',
	'0 to 9, :' => '0 à 9, :',
	'0 to 9, () + - . space' => 'de 0 à 9, () + -. espace',
	'0 to 9, - ' => '0 à 9, - ',
	'0 to 9, .- e +' => '0 à 9, .- e +',
	'0 to 9, a to z, A to Z' => '0 à 9, a à z, A à Z',
	'0 to 7' => '0 à 7',
	'0 to 9, a to z, A to Z, .-_+ @' => '0 à 9, a à z, A à Z, .-_+ @',
	'0 to 9, a to z, A to Z, .-_+ # / : ? % ~ [] @ ! $ = ; , &' => '0 à 9, a à z, A à Z, .-_+ # / : ? % ~ [] @ ! $ = ; , &',
	'0 to 9, a to z, A to Z, -_' => '0 à 9, a à z, A à Z, -_',
	
	// form error messages
	'Duplicate element name %s in this form for data table %s' => 'Duplication du nom d\'élément %s dans cette form pour table de données %s',
	'The form has expired, please submit the form again' => 'Forme expirée. Veuillez soumettre à nouveau.',
	'Token data type incorrect, possible hack attempt, data:%s:' => 'Données de jeton erronées, possibilité de tentative de piratage, données:%s:',
	'Possible hack attempt, hidden field %s came back with wrong data type, data:%s:' => 'Tentative de piratage possible, champ caché %s est revenu avec mauvais type de données, les données:%s:',
	'%s has a problem, %s' => '%s a un problème, %s',
	'invalid characters have been entered, valid characters are: %s, please modify your entry to reflect this. Thanks!' => 'Saisissement de charactères erroné, charactères valides sont les suivants : %s, Veuillez donc ressaisir votre entrée. Merci !',
	'the URL is too long' => 'Adresse trop longue',
	'the URL needs a period' => 'Adresse manque de point',
	'the URL does not exist' => 'Adresse non existante',
	'this Email is too long, please check and shorten it. Thanks!' => 'Email trop long. Veuillez composer un email plus court. Merci !',
	'this email needs an @ and a period, please check and add them. Thanks!' => 'Veuillez composer un email avec un arrobas et un point. Merci !',
	'Invalid characters have been entered, valid characters are: %s, please modify your entry to reflect this. Thanks!' => 'Charactères erronés saisis. Veuillez saisir les charactères %s. Merci!',
	'This URL is too long' => 'Adresse trop longue',
	'This URL needs a period' => 'Manque de point dans l\'adresse saisie',
	'This URL does not exist' => 'Adresse non existante',
	'This Email is too long, please check and shorten it. Thanks!' => 'Adresse email trop longue. Veuillez saisir un email plus court. Merci!',
	'This email needs an @ and a period, please check and add them. Thanks!' => 'Veuillez composer un email avec un arrobas et un point. Merci!',
	'Invalid element type: %s, element name: %s' => 'Type d\'élément erroné: %s, nom de l\'élément: %s',
	'%s is a required field, please enter a value. Thanks!"' => '%s est un champ obligatoire, veuillez entrer une valeur. Merci!',
	'Please ensure that %s has less than %s characters. Thanks!' => 'Veuillez vous assurer que %s est moins %s charactères. Merci!',
	'Please ensure that %s has at least %s characters. Thanks!' => 'veuillez vous assurer que %s contient des charactères d\'au moins %s. Merci!',
	'Value returned by %s not in valid results' => 'Valeur retournée par% s n\'est pas dans la validité des résultats',
	'This is a required field, please enter a value. Thanks!' => 'Champ obligatoire. Veuillez saisir une valeur. Merci!',
	'Please ensure that this has less than %2$s characters. Thanks!' => 'Veuillez vérifier que cela a moins de %2$s charactères. Merci!',
	'Please ensure that this has at least %2$s characters. Thanks!' => 'Veuillez vérifier que cela a au moins %2$s charactères . Merci!',
	'Value returned by this is not in valid results' => 'Valeur renvoyée par le sélecteur n\'est pas dans la validité des résultats',
	'* %s' => '* %s', // required field text added to label
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