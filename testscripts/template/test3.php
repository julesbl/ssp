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

$display = true;
$content = array(
    "title" => "test 3",
    "firstMultiple" => "First multiple replace",
    "include1" => "<p>Include 1 replace</p>",
    "secondMultiple" => "Second multiple replace",
    "thirdMultiple" => "Third multiple replace",
    "forthMultiple" => "Forth multiple replace"
    );

$page = new \w34u\ssp\Template($content, "test3.tpl", $display);

$page->includeTill("include2");
if($display){
    echo '<p>Include 2 replaced</p>';
}
else{
    $page->output .= '<p>Include 2 replaced</p>';
}
$page->includeTill();

if(!$display){
    echo $page->output;
}
?>