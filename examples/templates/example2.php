<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	example2.php
*   Created:	27-May-2010
*   Descrip:	Template routines Example 2: Sub templates.
*				Sub templates can be invoked in various ways.
*
*   Revision:	a
*   Rev. Date	27-May-2010
*   Descrip:	Created.
*/
require("../../sspadmin/includeheader.php");

$contentSub = array(
	"mainTitle" => "Main Title from sub content",
	"mainContent" => "Main content stuff from the sub content",
);
$content = array(
	"title" => 'Title for the page',
	"mainTitle" => "Main Title from main content",
	"mainContent" => "Main content stuff from the main content",
	"subTemplate" => new SSP_Template($contentSub, "example2Sub1.tpl"),
	"subTemplateMainData" => new SSP_Template("", "example2Sub1.tpl"),
);
$page = new SSP_Template($content, "example2.tpl", false);
echo $page->output();
?>