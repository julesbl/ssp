<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	example3.php
*   Created:	27-May-2010
*   Descrip:	Template routines Example 3: Conditional execution.
*				You can execute parts of the template conditionally on the existance of a data variable.
*
*   Revision:	a
*   Rev. Date	27-May-2010
*   Descrip:	Created.
*/
require("../../sspadmin/includeheader.php");

$content = array(
	"title" => 'Conditional execution',
	"mainTitle" => "Conditional execution of template parts",
	"mainContent" => "You can execute parts of the template conditionally on the existance of a data variable.",
);
if(isset($_GET["displayRest"])){
	$content["displayOther"] = "";
}
$page = new Template($content, "example3.tpl", false);
echo $page->output();
?>