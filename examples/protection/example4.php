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
*   Descrip:	Example 4 of protection scripts. Allows both user and admin users in.
*
*   Revision:	a
*   Rev. Date	28/03/2008
*   Descrip:	Created.
*/
// include the libraries
require("../../sspadmin/includeheader.php");
// start the session and protect the page against non user level users
$session = new Protect("user");
// everything beyond this point only accessible to the admin and user level users
echo "user name ".$session->userName."<br />";
echo "user access level ".$session->userAccessLevel."<br />";
echo "user email ".$session->userEmail."<br />";
echo "user ID ".$session->userId."<br />";
echo "logged in ".($session->loggedIn?"true":"false")."<br />";
echo "admin ".($session->admin?"true":"false")."<br />";
echo "session token ".$session->sessionToken."<br />";
echo "<br >";
if($session->isAccess("user")) echo "User or Above<br />";
if($session->isAccess("admin")) echo "Admin or above level user<br />";
if($session->isAccess("user", true)) echo "This is a user level user<br />";
if($session->isAccess("admin", true)) echo "This is a admin level user<br />";
if($session->admin) echo "This is a admin or above level user<br />";
?>