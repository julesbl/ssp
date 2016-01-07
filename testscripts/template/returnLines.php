<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    Template routines test
*   Routine:	test2.php
*   Created:	30/05/2006
*   Descrip:	Checking conditional if code.
*
*   Revision:	a
*   Rev. Date	30/05/2006
*   Descrip:	Created.
*/
error_reporting(E_ALL);
require_once("include.php");

$display = true;
$content = array(
    "title" => "Return Lines",
    "firstMultiple" => "First multiple replace",
    "include1" => "<p>Include 1 replace</p>",
    "secondMultiple" => "Second multiple replace",
    "thirdMultiple" => "Third multiple replace",
    "forthMultiple" => "Forth multiple replace",
	"blank" => "",
    );

$page = new SSP_Template($content, "returnLines.tpl", $display);
$page->numberReturnLines = 2;

$page->includeTill("include2");
if($display){
    echo '<p>Include 2 replaced</p>';
}
else{
    $page->output .= '<p>Include 2 replaced</p>';
}
$page->includeTill("end");

if(!$display){
    echo $page->output;
}
var_dump($page->returnedLines);
?>