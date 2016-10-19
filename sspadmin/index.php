<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	userlister.php
*   Created:	08/02/2005
*   Descrip:	Routine to list users.
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
*   Rev. Date	08/02/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	13/10/2006
*   Descrip:	Updated for new lister routines.
*
*   Revision:	c
*   Rev. Date	16/03/2007
*   Descrip:	Streamlined with new template processes and improved dessign.
*
*   Revision:	d
*   Rev. Date	14/01/2016
*   Descrip:	Composer implemented.
*
*   Revision:	e
*   Rev. Date	4/10/2016
*   Descrip:	Rebuilt admin using slim.
*/
namespace w34u\ssp;

use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

require 'includeheader.php';
$setup = [
	'settings' => [
		'displayErrorDetails' => true,
		],
];
$container = new \Slim\Container($setup);
// load protected session as service
$container['session'] = function($container) {
	return new Protect();
};
// load ssp admin setup as a service
$container['ssp'] = function($container){
	return new Setup($container['session'], true);
};
/**
 * Divert to login if not admin
 * @param Protect $session
 */
function ssp_logon($session){
	if(!$session->admin){
		SSP_Divert($session->cfg->logonScript);
	}
};

$app = new \Slim\App($container);
// home page
$app->any('/', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->session;
	ssp_logon($session);
	$ssp = $this->ssp;
	$lister = new UserLister($ssp);
	return $response->getBody()->write($lister->lister());
});
// delete a user
$app->any('/delete/{userId}', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->session;
	ssp_logon($session);
	$ssp = $this->ssp;
	$userId = $request->getAttribute('userId', '');
	$lister = new UserLister($ssp);
	return $response->getBody()->write($lister->deleteUser($userId));
});
// change filter
$app->any('/filterChange', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->session;
	ssp_logon($session);
	$ssp = $this->ssp;
	$lister = new UserLister($ssp);
	return $response->getBody()->write($lister->displayFilterForm());
});
// Change filter to admin pending
$app->any('/filterAdminPending', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->session;
	ssp_logon($session);
	$ssp = $this->ssp;
	$lister = new UserLister($ssp);
	$lister->filter->displayAdminPending();
	return $response->getBody()->write($lister->lister());
});
// Change filter to default listing
$app->any('/filterNormal', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->session;
	ssp_logon($session);
	$ssp = $this->ssp;
	$lister = new UserLister($ssp);
	$lister->filter->newSearch();
	return $response->getBody()->write($lister->lister());
});
// Admin user creation
$app->any('/adminusercreation', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->session;
	ssp_logon($session);
	$ssp = $this->ssp;
	
	$admin = new UserAdmin($session, $ssp, $session->userId);
	$result = $admin->userCreate();
	if($result === true){
		$tpl = $ssp->tpl(array('title' => 'User created', 'content' => '<h1>New user created</h1>'));
		return $response->getBody()->write($tpl->output());
	}
	else{
		return $response->getBody()->write($result);
	}
});

/**
 * User admin
 */
$app->group('/useradmin', function() use ($app) {
	// basic user information
	$app->any('/info/{userId}', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return $response->getBody()->write($admin->displayMisc());
	});
	// change basic user information
	$app->any('/chInfo', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return $response->getBody()->write($admin->userMisc(false, true));
	});
	// change password
	$app->any('/chPswd', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$userId = $request->getAttribute('userId');
		$needPassword = $request->getAttribute('needPassword');
		$admin = new UserAdmin($session, $ssp, $userId);
		return $response->getBody()->write($admin->changePassword("", $needPassword, true));
	});
	// change email
	$app->any('/chEmail', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$userId = $request->getAttribute('userId');
		$needPassword = $request->getAttribute('needPassword');
		$admin = new UserAdmin($session, $ssp, $userId);
		return $response->getBody()->write($admin->changeEmail($needPassword, true));
	});
	// change advanced user information
	$app->any('/chAdv', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return $response->getBody()->write($admin->changeAdmin());
	});
	// display advanced user information
	$app->any('/advInfo', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return $response->getBody()->write($admin->displayAdminInfo());
	});
	// send a join email
	$app->any('/joinEmail', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return $response->getBody()->write($admin->sendJoinupEmail());
	});
	// send an email to a user
	$app->any('/email', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return $response->getBody()->write($admin->emailUser($userId, $session->userId));
	});
})->add(function(Request $request, Response $response, $next){
	// Set up user admin for a particular user
	/* @var $session Protect */
	$session = $this->session;
	ssp_logon($session);
	if(!isset($_SESSION["adminUserId"])){
		$_SESSION["adminUserId"] = "";
	}
	$userId =& $_SESSION["adminUserId"];
	$route = $request->getAttribute('route');
	$arguments = $route->getArguments();
	if(isset($arguments['userId'])){
		$userId = $arguments['userId'];
	}
	$needPassword = ($session->userId === $userId);
	
	$request = $request->withAttribute('userId', $userId);
	$request = $request->withAttribute('needPassword', $needPassword);
	$response = $next($request, $response);
	return $response;
});

/**
 * Basic user functions such as login
 */
$app->group('/user', function() use ($app) {
	// user login
	$app->any('/logon', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		$contentMain = array();
		$ssp->pageTitleAdd('Logon');
		$tpl = $ssp->tpl($contentMain, "sspsmalltemplate.tpl", false);

		$login = new Logon($session, $tpl);
		$response->getBody()->write($login->output);
		return $response;
	});
	// user logoff
	$app->any('/logoff', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		
		$contentMain = array();
		$contentMain["title"] = "Logoff";
		$tpl = $ssp->tpl($contentMain, "sspsmalltemplate.tpl", false);

		$response->getBody()->write($session->logoff($tpl, true));
		return $response;
	});
	// start password recovery
	$app->any('/passwordrecover', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		
		$ssp->pageTitleAdd('Password recovery');
		$admin = new UserAdmin($session, $ssp, "", "", "sspsmalltemplate.tpl", false);
		$response->getBody()->write($admin->startPasswordRecovery());
		
		return $response;
	});
	// finish user password recovery by clicking on email link
	$app->any('/newpassword/{token}', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		
		$ssp->pageTitleAdd("Password recovery, enter new password");
		$admin = new UserAdmin($session, $ssp, "", "", "sspsmalltemplate.tpl", false);
		$token = $request->getAttribute('token', '');
		$response->getBody()->write($admin->finishPasswordRecovery($token));
		
		return $response;
	});
	// user confirmation on joinup
	$app->any('/userconfirm/{confirmToken}', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		
		$ssp->pageTitleAdd("User Confirmation of membership");
		$token = $request->getAttribute('confirmToken', '');
		$admin = new UserAdmin($session, $ssp, "", "sspsmalltemplate.tpl", false);
		$response->getBody()->write($admin->userConfirm($token));
		
		return $response;
	});
	// user joinup script
	$app->any('/usercreation', function(Request $request, Response $response){
		$session = $this->session;
		$ssp = $this->ssp;
		
		$ssp->pageTitleAdd("Join the site");
		$admin = new UserAdmin($session, $ssp, "", "", "sspsmalltemplate.tpl");
		$response->getBody()->write($admin->userJoin());
		
		return $response;
	});
});
$app->run();