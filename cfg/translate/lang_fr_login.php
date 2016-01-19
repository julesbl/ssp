<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	lang_fr_login.php
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
*   Rev. Date	22/05/2013
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	19-Jan-2016
*   Descrip:	Changed for composer.
*/
w34u\ssp\Translate::addToLanguage('fr', array(
	// protection object strings
	'Invalid page access level' => 'Niveau d\'accès erroné',
	'parameter pageCaching should be of type bool' => 'Paramètres pour la miss en cache devraient être de type bool',
	'parameter pageValid should be of type int' => 'Paramètres pour pageValid devraient être de type int',
	'parameter pageCheckEquals should be of type bool' => 'Paramètres pour pageCheckEquals devraient être de type bool',
	'Incorrect SSP_Protection parameters' => 'Paramètres SSP_Protection erronées',
	'Diverting to logon' => 'Déviation vers login',
	'General protection fault' => 'Erreur d\'ordre général',
	'Click here to login if divert does not happen within 5 seconds' => 'Cliquer ici pour ouvrir une session si vous n\'êtes pas dévié après 5 secondes',
	'Access control fault' => 'Erreur de contrôle d\'accès',
	// login screen texts
	'Email or password incorrect' => 'Email ou mot de passe erroné',
	'User name or password incorrect' => 'Pseudonyme ou mot de passe erroné',
	'Your email' => 'Votre email',
	'Your user name' => 'Votre pseudonyme',
	'Password' => 'Mot de passe',
	'Remember me (do not tick this box on a public computer)' => 'Se souvenir de moi (à éviter sur un ordinateur public)',
	// logged off screen title
	'Logged off' => 'Déconnecté',
	// login success page
	'Logon Success' => 'Bien connecté',
	'Welcome Back' => 'Re-bonjour',
	// password recovery
	'Password recovery' => 'Rétablissement mot de passe',
	'Enter your registered email' => 'Entrer votre email enregistré',
	'Recover Password' => 'Rétablir mot de passe',
	'New password succesfully entered' => 'Nouveau mot de passe accepté',
	'Invalid recovery link, please check you have used the correct recovery email, if problems persist please contact site admin' => 'Lien de récupération erroné. Veuillez vérifier le mail pour rétablissement. En cas où le problème persiste, veuillez contacter l\'administrateur du site',
	'Invalid recovery link, please check the email supplied and ensure you enter the whole of the url supplied, if problems persist please contact site admin' => 'Lien de récupération erroné, veuillez vérifier le mail pour vous assurer que vous avez bien copié l\'adresse. En cad où le problème persiste, veuillez contacter l\'administrateur du site',
	// join up form
	'First name' => 'Prénom',
	'Last name' => 'Nom',
	'Your email' => 'Votre email',
	'Your password' => 'Votre mot de passe',
	'Enter password again' => 'Ressaisissez votre mot de passe',
));
/* End of file lang_fr.php */
/* Location: /translate/ */