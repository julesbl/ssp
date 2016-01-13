<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project: 	Simple Site Protection
*   Routine:	logon.php
*   Created:	20/01/2005
*   Descrip:	Script to logon as a user.
*
*   Copyright 2005-2009 Julian Blundell, w34u
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
*   Rev. Date	20/01/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	19/08/2007
*   Descrip:	Totally revised.
*/


namespace w34u\ssp;

require 'includeheader.php';


$session = new Protect("", false, false);
$ssp = new Setup($session);

$contentMain = array();
$ssp->pageTitleAdd('Logon');
$tpl = $ssp->tpl($contentMain, "sspsmalltemplate.tpl", false);

$login = new Logon($session, $tpl);
echo $login->output;
?>