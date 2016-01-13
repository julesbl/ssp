<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	SSP - Example scripts
*   Routine:	example1.php
*   Created:	28/03/2008
*   Descrip:	Example 1 of protection scripts. Basic protection of a page.
*
*   Revision:	a
*   Rev. Date	28/03/2008
*   Descrip:	Created.
*/
// include the libraries
require("../../sspadmin/includeheader.php");
// start the session and protect the page against non admin users
$session = new Protect("admin");
// everything beyond this point only accessible to the admin user
echo "user name ".$session->userName."<br />";
echo "user access level ".$session->userAccessLevel."<br />";
echo "user email ".$session->userEmail."<br />";
echo "user ID ".$session->userId."<br />";
echo "logged in ".($session->loggedIn?"true":"false")."<br />";
echo "admin ".($session->admin?"true":"false")."<br />";
echo "session token ".$session->sessionToken."<br />";
echo "<br >";

?>