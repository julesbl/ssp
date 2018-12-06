<?php
namespace w34u\ssp;
/**
 *   Site by w34u
 *   http://www.w34u.com
 *   info@w34u.com
 *   +44 (0)1273 201344
 *   +44 (0)7833 512221
 *
 *   Project:    Simple Site Protection
 *   Routine:    user.php
 *   Created:    06/12/18
 *   Descrip:    User page.
 *
 *   Copyright 2005-2019 Julian Blundell, w34u
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
 *   Rev. Date    06/12/18
 *   Descrip:    Created.
 */
error_reporting(E_ALL);
require 'sspadmin/includeheader.php';
$SSP_Config = Configuration::getConfiguration();
$session= new Protect('user');
$content = [];
$content["title"] = "SSP Demo site user page";
$tpl_content = get_object_vars($session->userInfo);
$menu = new MenuGen();
$menu->add('/', "Home");
$menu->add($SSP_Config->adminDir, "Administration");
$menu->add('/user.php', "User page");
$content["menu"] = $menu->cMenu();
$tpl = new Template($tpl_content, "user.tpl");
$content['content'] = $tpl->output();
$page = new Template($content, "sspgeneraltemplate.tpl");
echo $page->output();
/**
 *   File name: user.php
 *   Path: user.php
 */