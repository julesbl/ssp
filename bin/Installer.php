<?php
/**
 *   Site by w34u
 *   http://www.w34u.com
 *   info@w34u.com
 *   +44 (0)7833 512221
 *
 *   Project:    Simple Site Protection
 *   Routine:    Installer.php
 *   Created:    19/03/2020
 *   Descrip:    Installation routines for composer.
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
 *   Revision:    a
 *   Rev. Date    19/03/2020
 *   Descrip:    Created.
 */
namespace w34u\ssp;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer{
	/**
	 * @param Event $event
	 */
	public static function postInstall(Event $event){
		$composer = $event->getComposer();
		$autoload = $composer->getAutoloadGenerator();
	}
}
/**
 *   File name: Installer.php
 *   Path: bin/Installer.php
 */