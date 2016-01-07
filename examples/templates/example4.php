<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	example4.php
*   Created:	27-May-2010
*   Descrip:	Template routines Example 4: Different ways of loading data and templates.
*				Various ways of loading data and template files.
*
*   Revision:	a
*   Rev. Date	27-May-2010
*   Descrip:	Created.
*/
require("../../sspadmin/includeheader.php");

$moreContent = new stdClass();
$moreContent->moreContent = "Some more content";
$dynamicallyDefinedTemplate[] = "<h3>Dynamically defined template</h3>";
$dynamicallyDefinedTemplate[] = "<p>Creating a template on the fly, very useful for things like forms</p>";
$dynamicallyDefinedTemplate[] = "{:if:mainTitle} // line removed on execution";
$dynamicallyDefinedTemplate[] = "<p>Main content: {mainContent}</p>";
$dynamicallyDefinedTemplate[] = "{:endif:mainTitle} // line removed on execution";

$content = array(
	"title" => 'Different ways of loading data and templates',
	"mainTitle" => "Different ways of loading data and templates",
	"mainContent" => "The data can be loaded from objects and after the template object has been defined.",
	"dynamic" => new SSP_Template("", $dynamicallyDefinedTemplate, false),
);
$page = new SSP_Template($content, "example4.tpl", false);
$page->restart($moreContent);
echo $page->output();
?>