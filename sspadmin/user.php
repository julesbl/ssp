<?php

/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	user.php
*   Created:	03-Oct-2016
*   Descrip:	Controller for user .
*
*   Revision:	a
*   Rev. Date	03-Oct-2016
*   Descrip:	Created.
*/
namespace w34u\ssp;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require 'includeheader.php';

$app = new \Slim\App;
$app->get('/user/logon', function(Request $request, Response $response){
	die('test');
	$session = new Protect("", false, false);
	$ssp = new Setup($session);

	$contentMain = array();
	$ssp->pageTitleAdd('Logon');
	$tpl = $ssp->tpl($contentMain, "sspsmalltemplate.tpl", false);

	$login = new Logon($session, $tpl);
	
	$response->getBody()->write($login->output);
	return $response;
});
/* End of file user.php */
/* Location: ./sspadmin/user.php */