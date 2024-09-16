<?php namespace w34u\ssp;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
/**
 *   Site by w34u
 *   http://www.w34u.com
 *   info@w34u.com
 *   +44 (0)7833 512221
 *
 *   Project:	Simple Site protection
 *   Routine:	Slim_middleware.php
 *   Created:	16/09/2024
 *   Descrip:	Middleware for the slim controller in admin.
 *
 *   Copyright 2005-2024 Julian Blundell, w34u
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
 *   Rev. Date	07/02/2005
 *   Descrip:	Created.
 */

class Slim_middleware implements MiddlewareInterface
{
	private Protect $session;

	public function __construct(Protect $session)
	{
		$this->session = $session;
	}

	public function process(Request $request, RequestHandler $handler): Response
	{
		// Set up user admin for a particular user
		$this->session->requireAdmin();
		if(!isset($_SESSION["adminUserId"])){
			$_SESSION["adminUserId"] = "";
		}
		$userId =& $_SESSION["adminUserId"];
		$arguments = $request->getQueryParams();
		if(!empty($arguments['userId'])){
			$userId = $arguments['userId'];
		}
		$needPassword = ($this->session->userId === $userId);

		$request = $request->withAttribute('userId', $userId);
		$request = $request->withAttribute('needPassword', $needPassword);
		$response = $handler->handle($request);
		return $response;
	}
}