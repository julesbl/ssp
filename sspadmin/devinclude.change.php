<?php
/**
 *   Site by w34u
 *   http://www.w34u.com
 *   j.blundell@w34u.com
 *   +44 (0)7833 512221
 *
 *   Project:    Simple Site Protection
 *   Routine:    devinclude.php
 *   Created:    11/08/2020
 *   Descrip:    Include file for package, remove change from name if doing development
 *
 *   Copyright 2005-2020 Julian Blundell, w34u
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
 *   Rev. Date    11/08/2020
 *   Descrip:    Created.
 *
 */
/* @var $loader \Composer\Autoload\ClassLoader */
$loader->addPsr4('w34u\\ssp\\', __DIR__. '/../cfg/', true);
if(php_sapi_name() === 'cli-server' or isset($ssptesting)){
	if(file_exists(__DIR__. '/../cfg/Configuration.test.php')){
		require __DIR__. '/../cfg/Configuration.test.php';
	}
}
error_reporting(E_ALL);
ini_set('display_errors', 'stdout');
/* End of file devinclude.php */
/* Location: sspadmin/devinclude.php */