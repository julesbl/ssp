<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	SSP - Example scripts
*   Routine:	example2.php
*   Created:	28/03/2008
*   Descrip:	Example 5 of protection scripts. How to protect ajax scripts.
*
*   Revision:	a
*   Rev. Date	28/03/2008
*   Descrip:	Created.
*/
// include the libraries
require("../../sspadmin/includeheader.php");
// start the session and protect the page against non user level users
// global variable, prevent the protection class from divertingto the login routine on
// session failure, returns the string "false" instead to std out.
$noLoginDivert = true;
// prevent testing and update of random cookie
$noCookieUpdate = true;
$session = new SSP_Protect("user");
// do ajax output
echo "<h1>hello world</h1>";
// if the session fails for whatever reason the string "false" will be returned
?>