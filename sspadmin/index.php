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

use DI\Container;
use Psr\Http\Message\MessageInterface;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

require 'includeheader.php';

$container = new Container();
// load protected session as service
$container->set('session', function() {
	$session = new Protect();
	return $session;
});
// load ssp admin setup as a service
$container->set('ssp', function() {
	global $container;
	return new Setup($container->get('session'), true);
});
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->setBasePath('/sspadmin');

$app->addErrorMiddleware(true, false, false);

/**
 * Load body with page content and return the response
 * @param string $content
 * @param Response $response
 * @return MessageInterface|Response
 */
function ssp_display(Response $response, string $content){
	$body = $response->getBody();
	$body->write($content);
	return $response->withBody($body);
}

// home page
$app->any('/', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->get('session');
	$session->requireAdmin();
	$ssp = $this->get('ssp');
	$lister = new UserLister($ssp);
	return ssp_display($response, $lister->lister());
});
$app->get('/test', function (Request $request, Response $response) {
	return ssp_display($response, 'Hello World!');
});
// delete a user
$app->any('/delete/{userId}', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->get('session');
	$session->requireAdmin();
	$ssp = $this->get('ssp');
	$userId = $request->getAttribute('userId', '');
	$lister = new UserLister($ssp);
	return ssp_display($response, $lister->deleteUser($userId));
});
// change filter
$app->any('/filterChange', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->get('session');
	$session->requireAdmin();
	$ssp = $this->get('ssp');
	$lister = new UserLister($ssp);
	return ssp_display($response, $lister->displayFilterForm());
});
// Change filter to admin pending
$app->any('/filterAdminPending', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->get('session');
	$session->requireAdmin();
	$ssp = $this->get('ssp');
	$lister = new UserLister($ssp);
	$lister->filter->displayAdminPending();
	return ssp_display($response, $lister->lister());
});
// Change filter to default listing
$app->any('/filterNormal', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->get('session');
	$session->requireAdmin();
	$ssp = $this->get('ssp');
	$lister = new UserLister($ssp);
	$lister->filter->newSearch();
	return ssp_display($response, $lister->lister());
});
// Admin user creation
$app->any('/adminusercreation', function (Request $request, Response $response) {
	/* @var $session Protect */
	$session = $this->get('session');
	$session->requireAdmin();
	$ssp = $this->get('ssp');
	
	$admin = new UserAdmin($session, $ssp, $session->userId);
	$result = $admin->userCreate();
	if($result === true){
		$tpl = $ssp->tpl(array('title' => 'User created', 'content' => '<h1>New user created</h1>'));
		return ssp_display($response, $tpl->output());
	}
	else{
		return ssp_display($response, $result);
	}
});

/**
 * User admin
 */

$app->group('/useradmin', function(RouteCollectorProxy $group) {
	// basic user information
	$group->any('/info/{userId}', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$_SESSION["adminUserId"] = $userId;
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->displayMisc());
	});
	// change basic user information
	$group->any('/chInfo', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->userMisc(false, true));
	});
	// change password
	$group->any('/chPswd', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$needPassword = $request->getAttribute('needPassword');
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->changePassword("", $needPassword, true));
	});
	// change email
	$group->any('/chEmail', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$needPassword = $request->getAttribute('needPassword');
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->changeEmail($needPassword, true));
	});
	// Admin ok of user
	$group->any('/enableUser', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->userAdminOk());
	});
	// change advanced user information
	$group->any('/chAdv', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->changeAdmin());
	});
	// display advanced user information
	$group->any('/advInfo', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->displayAdminInfo());
	});
	// send a join email
	$group->any('/joinEmail', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->sendJoinupEmail());
	});
	// send an email to a user
	$group->any('/email', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		$userId = $request->getAttribute('userId');
		$admin = new UserAdmin($session, $ssp, $userId);
		return ssp_display($response, $admin->emailUser($userId, $session->userId));
	});
})->add(new Slim_middleware($container->get('session')));

/**
 * Basic user functions such as login
 */
$app->group('/user', function(RouteCollectorProxy $group) {
	// user login
	$group->any('/logon', function(Request $request, Response $response){
		$session = $this->get('session');
		/* @var $ssp Setup */
		$ssp = $this->get('ssp');
		$login = new Logon($session);
		$contentMain = ['content' => $login->do_login()];
		$ssp->pageTitleAdd('Logon');
		$tpl = $ssp->tpl($contentMain, "sspsmalltemplate.tpl", false);
		$body = $response->getBody();
		$body->write($tpl->output());
		return $response->withBody($body);
	});
	// user logoff
	$group->any('/logoff', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		
		$contentMain = array();
		$contentMain["title"] = "Logoff";
		$tpl = $ssp->tpl($contentMain, "sspsmalltemplate.tpl", false);

		ssp_display($response, $session->logoff($tpl, true));
		return $response;
	});
	// user email login
	$group->any('/emaillogin/{token}', function(Request $request, Response $response){
		$session = $this->get('session');
		$token = $request->getAttribute('token', '');
		$login = new Logon($session);
		$login->do_email_login($token);
	});
	// start password recovery
	$group->any('/passwordrecover', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		
		$ssp->pageTitleAdd('Password recovery');
		$admin = new UserAdmin($session, $ssp, "", "sspsmalltemplate.tpl", false);
		ssp_display($response, $admin->startPasswordRecovery());
		
		return $response;
	});
	// finish user password recovery by clicking on email link
	$group->any('/newpassword/{token}', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		
		$ssp->pageTitleAdd("Password recovery, enter new password");
		$admin = new UserAdmin($session, $ssp, "", "sspsmalltemplate.tpl", false);
		$token = $request->getAttribute('token', '');
		ssp_display($response, $admin->finishPasswordRecovery($token));
		
		return $response;
	});
	// user confirmation on joinup
	$group->any('/userconfirm/{confirmToken}', function(Request $request, Response $response){
		$session = $this->get('session');
		$ssp = $this->get('ssp');
		
		$ssp->pageTitleAdd("User Confirmation of membership");
		$token = $request->getAttribute('confirmToken', '');
		$admin = new UserAdmin($session, $ssp, "", "sspsmalltemplate.tpl", false);
		ssp_display($response, $admin->userConfirm($token));
		
		return $response;
	});
	// user joinup script
	$group->any('/usercreation', function(Request $request, Response $response){
		/* @var $session Protect */
		$session = $this->get('session');
		/* @var $ssp Setup */
		$ssp = $this->get('ssp');
		
		$ssp->pageTitleAdd("Join the site");
		$admin = new UserAdmin($session, $ssp, "", "sspsmalltemplate.tpl", false);
		$join = $admin->userJoin();
		if(!is_bool($join)){
			ssp_display($response, $join);
		}
		else{
			SSP_Divert($session->cfg->siteRoot);
		}		
		return $response;
	});
});
$app->run();