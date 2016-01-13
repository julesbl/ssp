<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	example1.php
*   Created:	27-May-2010
*   Descrip:	Template routines Example 1: basic use of the routines.
*				All content is escaped using htmlentities to prevent injection attacks
*
*   Revision:	a
*   Rev. Date	27-May-2010
*   Descrip:	Created.
*/
require("../../sspadmin/includeheader.php");

$htmlStuff = '<p class="thingy">Some html to display <strong>be very careful with html from databases</strong>.</p>';
$textWithBreaks = "first line \n second & $ £ line \n third line";
// data can either be an array or a simple object, in this case an array
$content = array(
	"someText" => 'Just some standard text < > & " these characters should be escaped',
	"Example1" => "Example 1",
	"escapedHtml" => $htmlStuff,
	"programNe" => $htmlStuff,
	"templateNe" => $htmlStuff,
	"textWithBreaksNoNl" => $textWithBreaks,
	"textWithBreaksNl2Br" => $textWithBreaks,
);
$page = new Template($content, "example1.tpl");
$page->ne("programNe");
echo $page->output();
?>