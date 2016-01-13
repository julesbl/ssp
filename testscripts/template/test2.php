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
*   Created:	12/05/2006
*   Descrip:	Same as test 1 but checking the find template code.
*
*   Revision:	a
*   Rev. Date	12/05/2006
*   Descrip:	Created.
*/
require_once("include.php");

$display = false;
$title = "Test 2";
$page = new Template($title, "test2.tpl", $display);

$page->includeTill("include1");
if($display){
    echo '<p>Include 1 replaced</p>';
}
else{
    $page->output .= '<p>Include 1 replaced</p>';
}
$page->includeTill("include2");
if($display){
    echo '<p>Include 2 replaced</p>';
}
else{
    $page->output .= '<p>Include 2 replaced</p>';
}
$page->displayFooter();

if(!$display){
    echo $page->output;
}
?>